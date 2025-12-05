<?php

namespace App\Jobs;

use App\Models\Log;
use App\Models\Operator\Channel;
use App\Models\Operator\Operator;
use App\Models\Operator\OperatorChannelStatistic;
use App\Models\Operator\OperatorStatistic;
use App\Models\Operator\OverallOperatorStatistics;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateDayStatisticsJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected $date;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->date = $date ?? Carbon::now()->toDateString();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::info('Starting Generate Day Statistics Job with date: ' . $this->date);
            $date = $this->date;
            $startDate = Carbon::parse($date)->startOfDay()->subHours(3);
            $endDate = Carbon::parse($date)->endOfDay()->subHours(3);
            $startOfWeek = Carbon::parse($date)->startOfWeek()->toDateString();
            $endOfWeek = Carbon::parse($date)->endOfWeek()->toDateString();

            // Кэширование операторов и каналов
            $allOperators = cache()->remember("allOperators_{$date}", 3600, fn() => Operator::select('operator_id')->get());
            $allChannels = cache()->remember("allChannels_{$date}", 3600, fn() => Channel::select('channel_id')->get());

            // Начало запросов статы(в данном случае оператора)
            // Запись времени начала
            $startTime = microtime(true);
            Log::info('Начало запросов статы(в данном случае оператора) $intervalSubquery');
            $intervalSubquery = DB::table('operator_logs')
                ->select('operator_id', DB::raw('SUM(interval_seconds) / 60.0 AS total_time'))
                ->fromSub(function ($query) use ($startDate, $endDate) {
                    $query->select(
                        'operator_id',
                        DB::raw('EXTRACT(EPOCH FROM (event_time - LAG(event_time) OVER (PARTITION BY operator_id ORDER BY event_time))) AS interval_seconds')
                    )
                        ->from('operator_logs')
                        ->whereBetween('event_time', [$startDate, $endDate])
                        ->where('event_type', 'outbox');
                }, 'interval_data')
                ->whereBetween('interval_seconds', [1, 900])
                ->groupBy('operator_id');

            // Запись времени начала
            $middleTime = microtime(true);
            $totalTime = $middleTime - $startTime;

            Log::info('$intervalSubquery end and start $productLogsSubquery, time: ' . $totalTime);

            $productLogsSubquery = DB::table('product_logs')
                ->select(
                    'operator_id',
                    DB::raw('SUM(CASE WHEN status = \'reg\' THEN 1 ELSE 0 END) AS reg_count'),
                    DB::raw('SUM(CASE WHEN status IN (\'fd\', \'fd_a\', \'fd_r\') THEN 1 ELSE 0 END) AS dep_count')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('operator_id');

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$productLogsSubquery end and start $statisticsQuery, time: ' . $totalTime);

            // Подзапрос по FD тикетам за день по операторам (type='fd', status='Approved') через связь user->operator_id
            $fdTicketsSubquery = DB::table('player_tickets')
                ->join('users', 'player_tickets.user_id', '=', 'users.id')
                ->select(
                    'users.operator_id',
                    DB::raw('COUNT(*) AS fd')
                )
                ->whereBetween('player_tickets.created_at', [$startDate, $endDate])
                ->where('player_tickets.type', 'fd')
                ->where('player_tickets.status', 'Approved')
                ->whereNotNull('users.operator_id')
                ->groupBy('users.operator_id');

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$fdTicketsSubquery end and start $statisticsQuery, time: ' . $totalTime);

            // Основной запрос для статистики операторов
            $statisticsQuery = DB::table('operator_logs')
                ->select(
                    'operator_logs.operator_id',
                    DB::raw('COUNT(DISTINCT CASE WHEN is_new_client = true THEN client_id END) AS new_client_chats'),
                    DB::raw('COUNT(DISTINCT client_id) AS total_clients'),
                    DB::raw('SUM(CASE WHEN event_type = \'inbox\' THEN 1 ELSE 0 END) AS inbox_messages'),
                    DB::raw('SUM(CASE WHEN event_type = \'outbox\' THEN 1 ELSE 0 END) AS outbox_messages'),
                    DB::raw('MIN(CASE WHEN event_type = \'outbox\' THEN event_time + interval \'3 hours\' END) AS start_time'),
                    DB::raw('MAX(CASE WHEN event_type = \'outbox\' THEN event_time + interval \'3 hours\' END) AS end_time'),
                    DB::raw('MAX(COALESCE(intervals.total_time, 0)) AS total_time'),
                    DB::raw('MAX(COALESCE(product_logs_data.reg_count, 0)) AS reg_count'),
                    DB::raw('MAX(COALESCE(product_logs_data.dep_count, 0)) AS dep_count'),
                    DB::raw('MAX(COALESCE(fd_data.fd, 0)) AS fd')
                )
                ->leftJoinSub($intervalSubquery, 'intervals', 'operator_logs.operator_id', '=', 'intervals.operator_id')
                ->leftJoinSub($productLogsSubquery, 'product_logs_data', 'operator_logs.operator_id', '=', 'product_logs_data.operator_id')
                ->leftJoinSub($fdTicketsSubquery, 'fd_data', 'operator_logs.operator_id', '=', 'fd_data.operator_id')
                ->whereBetween('operator_logs.event_time', [$startDate, $endDate])
                ->groupBy('operator_logs.operator_id');

            $operatorStatistics = $statisticsQuery->get();
            // Конец запроса статы операторов

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$statisticsQuery end and start $channelIntervalSubquery, time: ' . $totalTime);

            // Начало запроса статы каналов
            $channelIntervalSubquery = DB::table('operator_logs')
                ->select('channel_id', DB::raw('SUM(interval_seconds) / 60.0 AS total_time'))
                ->fromSub(function ($query) use ($startDate, $endDate) {
                    $query->select(
                        'channel_id',
                        DB::raw('EXTRACT(EPOCH FROM (event_time - LAG(event_time) OVER (PARTITION BY channel_id ORDER BY event_time))) AS interval_seconds')
                    )
                        ->from('operator_logs')
                        ->whereBetween('event_time', [$startDate, $endDate])
                        ->where('event_type', 'outbox');
                }, 'interval_data')
                ->whereBetween('interval_seconds', [1, 900])
                ->groupBy('channel_id');

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$channelIntervalSubquery end and start $channelProductLogsSubquery, time: ' . $totalTime);

            $channelProductLogsSubquery = DB::table('product_logs')
                ->select(
                    'c2d_channel_id AS channel_id',
                    DB::raw('SUM(CASE WHEN status = \'reg\' THEN 1 ELSE 0 END) AS reg_count'),
                    DB::raw('SUM(CASE WHEN status IN (\'fd\', \'fd_a\', \'fd_r\') THEN 1 ELSE 0 END) AS dep_count')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('channel_id');

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$channelProductLogsSubquery end and start $channelStatisticsQuery, time: ' . $totalTime);

            $channelStatisticsQuery = DB::table('operator_logs')
                ->select(
                    'operator_logs.channel_id',
                    DB::raw('COUNT(DISTINCT CASE WHEN is_new_client = true THEN client_id END) AS new_client_chats'),
                    DB::raw('COUNT(DISTINCT client_id) AS total_clients'),
                    DB::raw('SUM(CASE WHEN event_type = \'inbox\' THEN 1 ELSE 0 END) AS inbox_messages'),
                    DB::raw('SUM(CASE WHEN event_type = \'outbox\' THEN 1 ELSE 0 END) AS outbox_messages'),
                    DB::raw('MIN(CASE WHEN event_type = \'outbox\' THEN event_time + interval \'3 hours\' END) AS start_time'),
                    DB::raw('MAX(CASE WHEN event_type = \'outbox\' THEN event_time + interval \'3 hours\' END) AS end_time'),
                    DB::raw('MAX(COALESCE(intervals.total_time, 0)) AS total_time'),
                    DB::raw('MAX(COALESCE(product_logs_data.reg_count, 0)) AS reg_count'),
                    DB::raw('MAX(COALESCE(product_logs_data.dep_count, 0)) AS dep_count')
                )
                ->leftJoinSub($channelIntervalSubquery, 'intervals', 'operator_logs.channel_id', '=', 'intervals.channel_id')
                ->leftJoinSub($channelProductLogsSubquery, 'product_logs_data', function ($join) {
                    $join->on(DB::raw('CAST(operator_logs.channel_id AS VARCHAR)'), '=', 'product_logs_data.channel_id');
                })
                ->whereBetween('operator_logs.event_time', [$startDate, $endDate])
                ->groupBy('operator_logs.channel_id');

            $channelStatistics = $channelStatisticsQuery->get();
            // Конец запроса статы каналов

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$channelStatisticsQuery end and start $detailedIntervalSubquery, time: ' . $totalTime);

            //Подзапросы для операторов и каналов
            $detailedIntervalSubquery = DB::table('operator_logs')
                ->select('operator_id', 'channel_id', DB::raw('SUM(interval_seconds) / 60.0 AS total_time'))
                ->fromSub(function ($query) use ($startDate, $endDate) {
                    $query->select(
                        'operator_id',
                        'channel_id',
                        DB::raw('EXTRACT(EPOCH FROM (event_time - LAG(event_time) OVER (PARTITION BY operator_id, channel_id ORDER BY event_time))) AS interval_seconds')
                    )
                        ->from('operator_logs')
                        ->whereBetween('event_time', [$startDate, $endDate])
                        ->where('event_type', 'outbox');
                }, 'interval_data')
                ->whereBetween('interval_seconds', [1, 900])
                ->groupBy('operator_id', 'channel_id');

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$detailedIntervalSubquery end and start $detailedProductLogsSubquery, time: ' . $totalTime);

            $detailedProductLogsSubquery = DB::table('product_logs')
                ->select(
                    'operator_id',
                    'c2d_channel_id AS channel_id',
                    DB::raw('SUM(CASE WHEN status = \'reg\' THEN 1 ELSE 0 END) AS reg_count'),
                    DB::raw('SUM(CASE WHEN status IN (\'fd\', \'fd_a\', \'fd_r\') THEN 1 ELSE 0 END) AS dep_count')
                )
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('operator_id', 'channel_id');

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$detailedProductLogsSubquery end and start $detailedStatisticsQuery, time: ' . $totalTime);

            // Основной запрос для детальной статистики
            $detailedStatisticsQuery = DB::table('operator_logs')
                ->select(
                    'operator_logs.operator_id',
                    'operator_logs.channel_id',
                    DB::raw('COUNT(DISTINCT CASE WHEN is_new_client = true THEN client_id END) AS new_client_chats'),
                    DB::raw('COUNT(DISTINCT client_id) AS total_clients'),
                    DB::raw('SUM(CASE WHEN event_type = \'inbox\' THEN 1 ELSE 0 END) AS inbox_messages'),
                    DB::raw('SUM(CASE WHEN event_type = \'outbox\' THEN 1 ELSE 0 END) AS outbox_messages'),
                    DB::raw('MIN(CASE WHEN event_type = \'outbox\' THEN event_time + interval \'3 hours\' END) AS start_time'),
                    DB::raw('MAX(CASE WHEN event_type = \'outbox\' THEN event_time + interval \'3 hours\' END) AS end_time'),
                    DB::raw('MAX(COALESCE(intervals.total_time, 0)) AS total_time'),
                    DB::raw('MAX(COALESCE(product_logs_data.reg_count, 0)) AS reg_count'),
                    DB::raw('MAX(COALESCE(product_logs_data.dep_count, 0)) AS dep_count')
                )
                ->leftJoinSub($detailedIntervalSubquery, 'intervals', function ($join) {
                    $join->on('operator_logs.operator_id', '=', 'intervals.operator_id')
                        ->on('operator_logs.channel_id', '=', 'intervals.channel_id');
                })
                ->leftJoinSub($detailedProductLogsSubquery, 'product_logs_data', function ($join) {
                    $join->on('operator_logs.operator_id', '=', 'product_logs_data.operator_id')
                        ->on(DB::raw('CAST(operator_logs.channel_id AS VARCHAR)'), '=', 'product_logs_data.channel_id');
                })
                ->whereBetween('operator_logs.event_time', [$startDate, $endDate])
                ->groupBy('operator_logs.operator_id', 'operator_logs.channel_id');

            $detailedStatistics = $detailedStatisticsQuery->get();
            // Конец запросов сбора статы


            // Статистика для операторов и каналов
            $this->processStatistics($operatorStatistics, 'operator', $date, OperatorStatistic::class);
            $this->processStatistics($channelStatistics, 'channel', $date, OperatorStatistic::class);

            // Детальная статистика
            $transformedStatistics = $detailedStatistics->map(function ($item) use ($date) {
                return [
                    'operator_id' => $item->operator_id,
                    'channel_id' => $item->channel_id,
                    'new_client_chats' => $item->new_client_chats,
                    'total_clients' => $item->total_clients,
                    'inbox_messages' => $item->inbox_messages,
                    'outbox_messages' => $item->outbox_messages,
                    'start_time' => $item->start_time ? Carbon::parse($item->start_time)->toDateTimeString() : null,
                    'end_time' => $item->end_time ? Carbon::parse($item->end_time)->toDateTimeString() : null,
                    'total_time' => (int)$item->total_time,
                    'reg_count' => $item->reg_count,
                    'dep_count' => $item->dep_count,
                    'date' => $date,
                ];
            });

            foreach ($transformedStatistics as $stat) {
                OperatorChannelStatistic::query()->updateOrCreate(
                    ['operator_id' => $stat['operator_id'], 'channel_id' => $stat['channel_id'], 'date' => $stat['date']],
                    $stat
                );
            }


            // Сортировка операторов по количеству outbox сообщений
            $topOperators = $operatorStatistics->sortByDesc('outbox_messages')->take(10);

            // Формирование массива топ операторов
            $topOperatorsJson = $topOperators->map(function ($item) use ($allOperators) {
                $operatorName = $allOperators->firstWhere('operator_id', $item->operator_id)->name;
                return [
                    'operator_name' => $operatorName ?: $item->operator_id,
                    'outbox_messages' => $item->outbox_messages
                ];
            })->values();

            // Запись времени начала
            $totalTime = microtime(true) - $middleTime;
            $middleTime = microtime(true);

            Log::info('$detailedStatisticsQuery end and start $topChannelsQuery, time: ' . $totalTime);

            // Получаем топ 3 канала с разбивкой по дням недели
            $topChannelsQuery = DB::table('operator_logs')
                ->select(
                    'channel_id',
                    DB::raw('SUM(CASE WHEN event_type = \'inbox\' THEN 1 ELSE 0 END) AS inbox_messages'),
                    DB::raw('SUM(CASE WHEN EXTRACT(DOW FROM event_time) = 1 AND event_type = \'inbox\' THEN 1 ELSE 0 END) as sunday_messages'),
                    DB::raw('SUM(CASE WHEN EXTRACT(DOW FROM event_time) = 2 AND event_type = \'inbox\' THEN 1 ELSE 0 END) as monday_messages'),
                    DB::raw('SUM(CASE WHEN EXTRACT(DOW FROM event_time) = 3 AND event_type = \'inbox\' THEN 1 ELSE 0 END) as tuesday_messages'),
                    DB::raw('SUM(CASE WHEN EXTRACT(DOW FROM event_time) = 4 AND event_type = \'inbox\' THEN 1 ELSE 0 END) as wednesday_messages'),
                    DB::raw('SUM(CASE WHEN EXTRACT(DOW FROM event_time) = 5 AND event_type = \'inbox\' THEN 1 ELSE 0 END) as thursday_messages'),
                    DB::raw('SUM(CASE WHEN EXTRACT(DOW FROM event_time) = 6 AND event_type = \'inbox\' THEN 1 ELSE 0 END) as friday_messages'),
                    DB::raw('SUM(CASE WHEN EXTRACT(DOW FROM event_time) = 7 AND event_type = \'inbox\' THEN 1 ELSE 0 END) as saturday_messages')
                )
                ->whereBetween('operator_logs.event_time', [$startOfWeek, $endOfWeek])
                ->groupBy('channel_id')
                ->orderByDesc('inbox_messages')
                ->limit(3);

            $topChannels = $topChannelsQuery->get();

            // Формирование топ каналов с разбивкой по дням недели
            $topChannelsJson = $topChannels->map(function ($channel) use ($allChannels) {
                // Получаем имя канала или его ID
                $channelName = $allChannels->firstWhere('channel_id', $channel->channel_id)->name;

                return [
                    'channel_name' => $channelName ?: $channel->channel_id,
                    'total_inbox_messages' => $channel->inbox_messages,
                    'messages_by_day' => [
                        'Sunday' => $channel->sunday_messages,
                        'Monday' => $channel->monday_messages,
                        'Tuesday' => $channel->tuesday_messages,
                        'Wednesday' => $channel->wednesday_messages,
                        'Thursday' => $channel->thursday_messages,
                        'Friday' => $channel->friday_messages,
                        'Saturday' => $channel->saturday_messages,
                    ],
                ];
            });

            // Подсчитываем общую статистику
            $overallStats = [
                'total_new_client_chats' => $operatorStatistics->sum('new_client_chats'),
                'total_clients' => $operatorStatistics->sum('total_clients'),
                'total_inbox_messages' => $operatorStatistics->sum('inbox_messages'),
                'total_outbox_messages' => $operatorStatistics->sum('outbox_messages'),
                'total_time' => (int)$operatorStatistics->sum('total_time'),
                'total_reg_count' => $operatorStatistics->sum('reg_count'),
                'total_dep_count' => $operatorStatistics->sum('dep_count'),
                'date' => $date,
                'top_operators' => $topOperatorsJson,
                'top_channels' => $topChannelsJson,
            ];

            // Сохраняем общую статистику через updateOrCreate
            OverallOperatorStatistics::query()->updateOrCreate(
                ['date' => $date], // Уникальный ключ для поиска записи
                $overallStats // Данные для обновления/вставки
            );


            $middleTime = microtime(true);
            $totalTime = $middleTime - $startTime;


            Log::info('end of statistic, time: ' . $totalTime);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    protected function processStatistics($statistics, string $entityType, $date, $model): void
    {
        $transformedStatistics = $statistics->map(function ($item) use ($entityType, $date) {
            $fd = (int)($item->fd ?? 0);
            $totalClients = (int)($item->total_clients ?? 0);
            $cr = $totalClients > 0 ? round(($fd / $totalClients) * 100, 2) : 0;
            return [
                'entity_id' => $item->operator_id ?? $item->channel_id ?? null,
                'entity_type' => $entityType,
                'new_client_chats' => $item->new_client_chats,
                'total_clients' => $item->total_clients,
                'inbox_messages' => $item->inbox_messages,
                'outbox_messages' => $item->outbox_messages,
                'start_time' => $item->start_time ? Carbon::parse($item->start_time)->toDateTimeString() : null,
                'end_time' => $item->end_time ? Carbon::parse($item->end_time)->toDateTimeString() : null,
                'total_time' => (int)$item->total_time,
                'reg_count' => $item->reg_count,
                'dep_count' => $item->dep_count,
                'fd' => $fd,
                'cr_dialog_to_fd' => $cr,
                'date' => $date,
            ];
        });

        foreach ($transformedStatistics as $stat) {
            $model::query()->updateOrCreate(
                ['entity_id' => $stat['entity_id'], 'entity_type' => $stat['entity_type'], 'date' => $stat['date']],
                $stat
            );
        }
    }
}
