<?php

namespace App\Services\Operator;

use App\DTOs\AiRetentionReportDto;
use App\DTOs\AiRetentionReportListDto;
use App\Models\Log as DbLog;
use App\Models\Operator\AiRetentionReport;
use App\Models\Operator\AiRetentionReportTest;
use App\Services\ApiTokenService;
use App\Services\SettingsService;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OpenAI;
use OpenAI\Client;

class AiRetentionReportGenerator
{
    public function __construct(
        protected SettingsService $settingsService,
        protected ApiTokenService $apiTokenService
    )
    {
    }


    /**
     * Сгенерировать AI-отчёты по операторам и каналам
     *
     * @param array $operatorIds - ID операторов
     * @param array $channelIds - ID каналов
     * @param string|null $date - Дата для обработки (формат Y-m-d). Если null, используется вчерашний день
     * @param int|null $limit - Максимальное количество отчётов (null = без ограничения)
     * @param bool $save - Сохранять ли отчёты в БД (false = тест)
     * @param int|null $testSaveUserId - Если указан и $save=false, сохраняем результаты в test-таблицу для данного пользователя
     */
    public function generate(array $operatorIds, array $channelIds, ?string $date = null, ?int $limit = null, bool $save = false, ?int $testSaveUserId = null): array
    {
        $t0 = microtime(true);
        DbLog::info("[AI-Retention] generate: start | operators=" . count($operatorIds) . ", channels=" . count($channelIds) . ", limit=" . ($limit ?? 'null') . ", save=" . ($save ? '1' : '0'));

        $promptTemplate = $this->settingsService->get('channel_prompt');
        $openai = $this->getOpenAiClient();

        $tFetch0 = microtime(true);
        $messages = $this->getMessages($operatorIds, $channelIds, $date);
        $tFetch1 = microtime(true);
        DbLog::info("[AI-Retention] generate: fetched messages=" . $messages->count() . " in " . number_format(($tFetch1 - $tFetch0), 3) . "s");

        $tGroup0 = microtime(true);
        $dialogs = $this->groupMessages($messages);
        $tGroup1 = microtime(true);
        DbLog::info("[AI-Retention] generate: grouped dialogs=" . count($dialogs) . " in " . number_format(($tGroup1 - $tGroup0), 3) . "s");

        // Курсор: только в тестовом режиме (save=false) пропускаем уже обработанные диалоги
        $cursorApplied = false;
        $currentDate = $this->extractCommonConversationDate($dialogs);
        if (!$save && $currentDate !== null) {
            $originalCount = count($dialogs);
            [$dialogs, $cursorApplied] = $this->applyCursor($dialogs, $currentDate);
            if ($cursorApplied) {
                DbLog::info("[AI-Retention] generate: cursor applied for date={$currentDate}, remaining=" . count($dialogs) . "/{$originalCount}");
            }
        }

        // В тестовом режиме фильтруем диалоги без operator_id (чтобы не падать и не тратить токены)
        if ($save === false) {
            $before = count($dialogs);
            // Важно: не сбрасывать ключи, чтобы курсор работал по ключу op_{client}_{operator}
            $dialogs = array_filter($dialogs, fn(array $d) => !empty($d['operator_id']));
            $after = count($dialogs);
            if ($after !== $before) {
                DbLog::info("[AI-Retention] generate: filtered dialogs without operator_id: removed=" . ($before - $after) . ", left={$after}");
            }
        }

        // Если задан лимит, будем набирать до лимита валидных результатов,
        // не обрезая исходный пул диалогов заранее (чтобы в тестовом режиме
        // не застревать на первых N диалогах, которые могут быть отфильтрованы)
        if ($limit !== null) {
            DbLog::info("[AI-Retention] generate: will collect up to {$limit} valid operator-client dialogs");
        }

        // В тестовом режиме убраны ограничения по времени и количеству попыток — управление временем на стороне воркера
        $attemptCap = null;
        $deadlineAt = null;

        $results = [];
        $processed = 0;
        $succeeded = 0;

        $lastProcessedKey = null;
        $lastAttemptedKey = null;
        foreach ($dialogs as $key => $dialog) {
            if ($limit !== null && count($results) >= $limit) {
                break;
            }
            $processed++;
            $currentKey = is_string($key) ? $key : ("op_" . ($dialog['client_id'] ?? 'null') . "_" . ($dialog['operator_id'] ?? 'null'));

            // В тестовом режиме пишем в test-таблицу только до достижения лимита
            $allowTestSave = (!$save && $testSaveUserId !== null && ($limit === null || count($results) < $limit));
            $dto = $this->processDialog($dialog, $promptTemplate, $openai, $save, $allowTestSave ? $testSaveUserId : null);

            if ($dto) {
                $results[] = $dto;
                $succeeded++;
                $lastProcessedKey = $currentKey;

                // Немедленно останавливаемся при достижении лимита, чтобы не начинать следующую итерацию и не тратить токены
                if ($limit !== null && count($results) >= $limit) {
                    DbLog::info("[AI-Retention] generate: stopped by limit (collected={$limit})");
                    // Зафиксируем актуальный курсор в тестовом режиме
                    if (!$save && $currentDate !== null) {
                        $this->setCursor($currentDate, $lastProcessedKey);
                    }
                    break;
                }
            } else {
                $lastAttemptedKey = $currentKey;
            }

            // Инкрементальное обновление курсора после каждой попытки (успех/пропуск)
            if (!$save && $currentDate !== null) {
                $this->setCursor($currentDate, $currentKey);
            }
        }

        $t1 = microtime(true);
        DbLog::info("[AI-Retention] generate: done | processed={$processed}, succeeded={$succeeded}, total_time=" . number_format(($t1 - $t0), 3) . "s");

        // Обновляем курсор только в тестовом режиме
        if (!$save && $currentDate !== null) {
            if ($lastProcessedKey !== null) {
                $this->setCursor($currentDate, $lastProcessedKey);
                DbLog::info("[AI-Retention] generate: cursor set to key={$lastProcessedKey} for date={$currentDate}");
            } elseif ($lastAttemptedKey !== null) {
                // Продвигаем курсор даже если валидных результатов не было — чтобы не застревать на тех же диалогах
                $this->setCursor($currentDate, $lastAttemptedKey);
                DbLog::info("[AI-Retention] generate: cursor advanced to last attempted key={$lastAttemptedKey} for date={$currentDate}");
            } elseif ($cursorApplied) {
                // Если курсор был применён и ничего не обработали — сброс (wrap-around)
                $this->clearCursor();
                DbLog::info("[AI-Retention] generate: cursor cleared (end reached)");
            }
        }
        return $results;
    }

    /**
     * Получить OpenAI клиент
     */
    private function getOpenAiClient(): Client
    {
        $token = $this->apiTokenService->getTokenByService('ChatGPT');

        $httpClient = new HttpClient([
            'timeout' => 60,
            'connect_timeout' => 10,
        ]);

        return OpenAI::factory()
            ->withApiKey($token)
            ->withHttpClient($httpClient)
            ->make();
    }

    /**
     * Получить сообщения из БД
     */
    private function getMessages(array $operatorIds, array $channelIds, ?string $date = null): Collection
    {
        // Если дата не указана, используем вчерашний день
        if ($date === null) {
            $start = now()->subDay()->startOfDay();
            $end = now()->subDay()->endOfDay();
        } else {
            // Валидация формата даты
            $dateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $date);
            $start = $dateTime->copy()->startOfDay();
            $end = $dateTime->copy()->endOfDay();
        }

        return DB::table('data.p_raw_chat2desk_json_msg as m')
            ->select([
                'm.client_id',
                'm.operator_id',
                'm.channel_id',
                'm.type',
                'm.text',
                'm.created_at',
            ])
            // Используем between по created_at, чтобы не ломать индексы функциями по колонке
            ->whereBetween('m.created_at', [$start, $end])
            ->whereIn('m.type', ['from_client', 'to_client'])
            // Пересечение фильтров по операторам и каналам, если они заданы
            ->when(!empty($operatorIds), fn($q) => $q->whereIn('m.operator_id', $operatorIds))
            ->when(!empty($channelIds), fn($q) => $q->whereIn('m.channel_id', $channelIds))
            ->orderBy('m.client_id')
            ->orderBy('m.created_at')
            ->get();
    }

    /**
     * Группировка сообщений по client_id + operator и client_id + channel
     */
    private function groupMessages(Collection $messages, bool $testMode = false, string $prefer = 'operator'): array
    {
        $dialogs = [];

        foreach ($messages as $msg) {
            $text = trim($msg->text ?? '');
            if ($text === '') {
                continue;
            }

            $role = $msg->type === 'from_client' ? 'Клиент' : 'Оператор';
            $date = date('Y-m-d', strtotime($msg->created_at));
            // Группировка только по оператору (client_id + operator_id)
            $keyOperator = "op_{$msg->client_id}_{$msg->operator_id}";
            if (!isset($dialogs[$keyOperator])) {
                $dialogs[$keyOperator] = [
                    'client_id' => $msg->client_id,
                    'operator_id' => $msg->operator_id,
                    'channel_id' => $msg->channel_id ?? null,
                    'conversation_date' => $date,
                    'messages' => [],
                ];
            }
            $dialogs[$keyOperator]['messages'][] = "{$role}: {$text}";
        }

        // Ранее фильтровали по >=3 сообщений; теперь возвращаем все диалоги
        return $dialogs;
    }

    /**
     * Если у всех диалогов одна дата, вернуть её; иначе null.
     */
    private function extractCommonConversationDate(array $dialogs): ?string
    {
        $date = null;
        foreach ($dialogs as $dialog) {
            $d = $dialog['conversation_date'] ?? null;
            if ($d === null) {
                return null;
            }
            if ($date === null) {
                $date = $d;
            } elseif ($date !== $d) {
                return null;
            }
        }
        return $date;
    }

    /**
     * Применить курсор: пропустить диалоги до последнего обработанного ключа для указанной даты.
     * Возвращает [dialogsAfterCursor, applied(bool)]. При достижении конца – выполняется wrap-around (сброс курсора в generate).
     */
    private function applyCursor(array $dialogs, string $date): array
    {
        $cursor = $this->getCursor();
        if (!$cursor) {
            return [$dialogs, false];
        }

        $keys = array_keys($dialogs);
        $cursorKey = $cursor['key'] ?? null;
        if (!is_string($cursorKey) || $cursorKey === '') {
            return [$dialogs, false];
        }

        $pos = array_search($cursorKey, $keys, true);
        if ($pos === false) {
            // Ключ не найден в текущем наборе — вероятно, новый день или иная выборка
            return [$dialogs, false];
        }

        // Вернуть элементы после позиции курсора, сохраняя ключи
        $sliced = array_slice($dialogs, $pos + 1, null, true);
        if (count($sliced) === 0) {
            // Пусть вызывающий решит, что делать (сбросить курсор и начать сначала)
            return [$dialogs, true];
        }
        return [$sliced, true];
    }

    private function getCursor(): ?array
    {
        try {
            $raw = $this->settingsService->get('ai_retention_cursor');
            if (!is_string($raw) || $raw === '') {
                return null;
            }
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function setCursor(string $date, string $key): void
    {
        $payload = json_encode(['date' => $date, 'key' => $key], JSON_UNESCAPED_UNICODE);
        $this->settingsService->set('ai_retention_cursor', $payload);
    }

    private function clearCursor(): void
    {
        $this->settingsService->set('ai_retention_cursor', '');
    }
    /**
     * Обработка диалога и генерация отчёта через GPT
     */
    private function processDialog(array $dialog, string $template, Client $openai, bool $save, ?int $testSaveUserId = null): AiRetentionReportListDto|AiRetentionReportDto|null
    {
        try {
            $dialogText = implode("\n", $dialog['messages']);

            $clientId = $dialog['client_id'] ?? 'null';
            $operatorId = $dialog['operator_id'] ?? 'null';

            // Если отчёт для этой пары client/operator и даты уже существует
            // и его raw_payload.messages идентичен текущему диалогу —
            // пропускаем вызов OpenAI, чтобы не тратить токены.
            if ($save) {
                $existing = AiRetentionReport::query()->where([
                    'client_id' => (int) $dialog['client_id'],
                    'operator_id' => $dialog['operator_id'] ?? null,
                    'conversation_date' => $dialog['conversation_date'],
                ])->first(['id', 'raw_payload', 'client_id', 'operator_id', 'conversation_date', 'score', 'comment', 'analysis', 'created_at']);

                if ($existing) {
                    $existingMessages = (array) ($existing->raw_payload['messages'] ?? []);
                    if ($existingMessages === $dialog['messages']) {
                        DbLog::info("[AI-Retention] processDialog: skipped (unchanged raw_payload) op={$operatorId}, client={$clientId}");
                        return AiRetentionReportDto::fromModel($existing);
                    }
                }
            }
            // Контекст клиент/оператор + встроенная подстановка диалога
            $contextHeader = "Диалог с клиентом: {$clientId}";
            $hasPlaceholder = strpos($template, '{dialog}') !== false;
            if ($hasPlaceholder) {
                $promptBody = str_replace('{dialog}', $dialogText, $template);
            } else {
                $promptBody = rtrim($template) . "\n\nДиалог:\n" . $dialogText;
            }

            $prompt = $contextHeader . "\n\n" . $promptBody;
            $prompt .= "\n\nСФОРМИРУЙ ОТВЕТ СТРОГО В ВИДЕ ОДНОГО JSON-ОБЪЕКТА НА ОДНОЙ СТРОКЕ, БЕЗ ЛИШНЕГО ТЕКСТА И БЕЗ МАРКДАУНА.\n";
            $prompt .= 'Формат: {"operator": ' . $operatorId . ', "client_id": ' . $clientId . ', "score": <число 1-5>, "comment_gpt": "<комментарий>", "analysis": "<разбор>"}';

            // Логируем входной промпт: есть ли плейсхолдер, длина диалога, превью начала и хвоста
            DbLog::info(
                "[AI-Retention] prompt: op={$operatorId}, client={$clientId}, len=" . strlen($prompt) .
                ", has_placeholder=" . ($hasPlaceholder ? '1' : '0') .
                ", dialog_chars=" . strlen($dialogText) .
                ", head=" . mb_substr($prompt, 0, 300)
            );
            DbLog::info("[AI-Retention] prompt_tail: op={$operatorId}, client={$clientId}, tail=" . mb_substr($prompt, -300));

            $callStart = microtime(true);
            $response = $openai->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);
            $callTime = microtime(true) - $callStart;

            $content = trim($response->choices[0]->message->content ?? '');
            // Логируем ответ целиком (обрезанный), чтобы понимать, почему не распарсилось
            DbLog::info("[AI-Retention] response: op={$operatorId}, client={$clientId}, len=" . strlen($content) . ", preview=" . mb_substr($content, 0, 800));
            DbLog::info("[AI-Retention] processDialog: raw_content=" . mb_substr($content, 0, 400));

            // Извлекаем score и comment_gpt из полнотекстового ответа
            $score = null;
            $comment = '';
            // Пытаемся распарсить как JSON (предпочтительно)
            $score = null;
            $comment = '';
            $analysis = '';
            $json = $content;
            if (str_starts_with($json, '```')) {
                // Удаляем возможные кодовые блоки ```json ... ```
                $json = preg_replace('/^```[a-zA-Z]*\n?|```$/m', '', $json);
                $json = trim($json);
            }
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $score = isset($decoded['score']) ? (int) $decoded['score'] : null;
                $comment = isset($decoded['comment_gpt']) ? trim((string) $decoded['comment_gpt']) : '';
                $analysis = isset($decoded['analysis']) ? trim((string) $decoded['analysis']) : '';
            }
            // Фоллбэк: более надежный разбор из неструктурированного текста
            if ($score === null || $comment === '') {
                if (preg_match('/score\s*=\s*([1-5])\b/i', $content, $m)) {
                    $score = (int) ($m[1] ?? null);
                }
                if (preg_match('/comment_gpt\s*=\s*(.+?)(?=\bscore\b|$)/is', $content, $m)) {
                    $comment = trim($m[1] ?? '');
                    $comment = rtrim($comment, " ,/\n\r\t");
                }
            }

            $data = [
                'client_id' => $dialog['client_id'],
                'operator_id' => $dialog['operator_id'] ?? null,
                'channel_id' => $dialog['channel_id'] ?? null,
                'score' => $score,
                'comment' => $comment,
                'analysis' => $analysis !== '' ? $analysis : $content,
                'conversation_date' => $dialog['conversation_date'],
            ];

            DbLog::info("[AI-Retention] processDialog: op=" . ($dialog['operator_id'] ?? 'null') . ", ch=" . ($dialog['channel_id'] ?? 'null') . ", client=" . ($dialog['client_id'] ?? 'null') . ", msgs=" . count($dialog['messages']) . ", openai_time=" . number_format($callTime, 3) . "s");

            // В тестовом режиме пропускаем диалоги без operator_id, т.к. DTO требует int
            if (!$save && empty($dialog['operator_id'])) {
                return null;
            }

            // В тестовом режиме также пропускаем пустые результаты (без score или comment)
            if (!$save && ($score === null || $comment === '')) {
                DbLog::info("[AI-Retention] processDialog: skipped due to empty score/comment in test mode");
                return null;
            }

            if ($save) {
                $saveStart = microtime(true);
                // Upsert по (client_id, operator_id, conversation_date) + сохранение raw_payload
                $report = AiRetentionReport::query()->updateOrCreate(
                    [
                        'client_id' => $data['client_id'],
                        'operator_id' => $data['operator_id'],
                        'conversation_date' => $data['conversation_date'],
                    ],
                    [
                        'channel_id' => $data['channel_id'],
                        'score' => $data['score'],
                        'comment' => $data['comment'],
                        'analysis' => $data['analysis'],
                        'raw_payload' => ['messages' => $dialog['messages']],
                    ]
                );
                $saveTime = microtime(true) - $saveStart;
                DbLog::info("[AI-Retention] processDialog: upserted=1, save_time=" . number_format($saveTime, 3) . "s");
                return AiRetentionReportDto::fromModel($report);
            }

            // save=false → тестовый режим: при наличии userId сохраняем в тестовую таблицу (idempotent)
            if ($testSaveUserId !== null) {
                $saveStart = microtime(true);
                AiRetentionReportTest::query()->updateOrCreate(
                    [
                        'client_id' => (int) $dialog['client_id'],
                        'operator_id' => (int) $dialog['operator_id'],
                        'conversation_date' => $dialog['conversation_date'],
                        'user_id' => $testSaveUserId,
                    ],
                    [
                        'score' => (int) $score,
                        'comment' => (string) $comment,
                        'analysis' => (string) ($analysis !== '' ? $analysis : $content),
                        'raw_payload' => ['messages' => $dialog['messages']],
                        'prompt' => $prompt,
                    ]
                );
                $saveTime = microtime(true) - $saveStart;
                DbLog::info("[AI-Retention] processDialog: test-saved=1, save_time=" . number_format($saveTime, 3) . "s");
            } else {
                DbLog::info("[AI-Retention] processDialog: saved=0, save_time=0.000s");
            }
            return new AiRetentionReportListDto(
                id: 0,
                operator_id: (int) $data['operator_id'],
                client_id: (int) $data['client_id'],
                user_id: $testSaveUserId,
                score: $data['score'],
                comment: $data['comment'],
                analysis: (string) $data['analysis'],
                raw_payload: ['messages' => $dialog['messages']],
                conversation_date: $data['conversation_date'],
                prompt: $prompt
            );
        } catch (\Throwable $e) {
            DbLog::error("[AI-Retention] processDialog: error op=" . ($dialog['operator_id'] ?? 'null') . ", ch=" . ($dialog['channel_id'] ?? 'null') . ", client=" . ($dialog['client_id'] ?? 'null') . ": " . $e->getMessage());
            return null;
        }
    }
}
