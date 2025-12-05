<?php

namespace App\Jobs;

use App\Models\clients\Client;
use App\Models\clients\ClientsLog;
use App\Models\Operator\Channel;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class Chat2Desk implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected $clientsLog;

    public function __construct(ClientsLog $clientsLog)
    {
        $this->clientsLog = $clientsLog;
    }

    public function handle(): void
    {
        //Статус в процессе
        $this->clientsLog->task_status = "in_progress";
        $this->clientsLog->started_at = Carbon::now('UTC');
        $this->clientsLog->save();

        try {
            $webhookData = json_decode($this->clientsLog->webhook_data, true); // получаем дату хука в списке

            if (isset($webhookData['clickid'])) {
                $client = Client::query()->where('tg_id', $webhookData['tg_id'])->where('c2d_channel_id', $webhookData['c2d_channel_id']);
                $log = ClientsLog::query()
                    ->where('client_id', (int)$webhookData['tg_id'])
                    ->whereRaw("webhook_data->>'hook_type' = ?", ['new_client'])
                    ->whereRaw("EXISTS (SELECT 1 FROM jsonb_array_elements(webhook_data->'channels') AS ch WHERE ch->>'id' = ?)", [(int)$webhookData['c2d_channel_id']])
                    ->first();
                if (!$client->exists()) {
                    $clientData = [
                        'is_c2d' => 1,
                        'tg_id' => $webhookData['tg_id'],
                        'c2d_channel_id' => $webhookData['c2d_channel_id'],
                        'clickid' => $webhookData['clickid'],
                        'source_id' => 'Chat2Desk',
                        'is_c2d_date' => Carbon::now('UTC'),
                    ];

                    if ($log) {
                        $clientData['c2d_client_id'] = $log->c2d_client_id;
                    }

                    FetchKeitaroData::dispatch($clientData['clickid'], $this->clientsLog, $clientData)->onQueue('high-priority');
                } else {
                    $client = $client->first();
                    if ($log) {
                        $client->c2d_client_id = $log->c2d_client_id;
                        $client->save();
                    }
                    $this->checkC2dDate($client);

                    $this->clientsLog->task_status = "done";
                    $this->clientsLog->result = "Client Already Exist";
                    $this->clientsLog->finished_at = Carbon::now('UTC');
                    $this->clientsLog->save();
                }
            } else {
                // если хук inbox
                if ($webhookData['hook_type'] == "inbox") {
                    $clientData = [
                        'is_c2d' => 1,
                        'tg_id' => $this->clientsLog->client_id,
                        'c2d_last_mssg' => $this->clientsLog->created_at,
                        'c2d_channel_id' => $webhookData['channel_id'],
                        'c2d_client_id' => $this->clientsLog->c2d_client_id,
                    ];
                    if (Client::query()
                        ->where('tg_id', $clientData['tg_id'])
                        ->where('c2d_channel_id', $clientData['c2d_channel_id'])
                        ->exists()) // если клиент уже есть
                    {
                        $client = Client::query()
                            ->where('tg_id', $clientData['tg_id'])
                            ->where('c2d_channel_id', $clientData['c2d_channel_id'])
                            ->first();

                        $this->checkC2dDate($client);
                        $client->update($clientData);

                        Channel::query()->firstOrCreate(['channel_id' => $clientData['c2d_channel_id']]);

                        $this->clientsLog->task_status = "done";
                        $this->clientsLog->result = "Client Updated";
                        $this->clientsLog->finished_at = Carbon::now('UTC');
                        $this->clientsLog->save();
                    } else {
                        $this->clientsLog->task_status = "done";
                        $this->clientsLog->result = "Client Not Found";
                        $this->clientsLog->finished_at = Carbon::now('UTC');
                        $this->clientsLog->save();
                    }
                    // Если хук с tags
                } else if ($webhookData['hook_type'] == "add_tag_to_client") {
                    $client = Client::query()
                        ->where('c2d_client_id', $webhookData['client_id'])
                        ->where('c2d_channel_id', $webhookData['channel_id'])
                        ->first();

                    if ($client) { // Если клиент найден
                        // Преобразуем старый формат тегов в массив
                        $tags = json_decode($client->c2d_tags, true) ?? []; // Если null, ставим пустой массив
                        $tags[] = (int)$webhookData['id']; // Добавляем новый тег
                        $tags = array_values(array_unique($tags)); // Убираем дубли

                        $this->extracted($webhookData, $tags, $client);
                        $this->checkC2dDate($client);
                        $this->clientsLog->result = "Added Tag To Client";
                    } else {
                        $this->clientsLog->result = "Client Not Found";
                    }

                    $this->clientsLog->task_status = "done";
                    $this->clientsLog->finished_at = Carbon::now('UTC');
                    $this->clientsLog->save();
                } else if ($webhookData['hook_type'] == "delete_tag_from_client") {
                    $clients = Client::query()
                        ->where('c2d_client_id', $webhookData['client_id'])
                        ->get();

                    if ($clients->isNotEmpty()) { // Если есть клиенты
                        foreach ($clients as $client) {
                            // Преобразуем теги из JSON в массив
                            $tags = json_decode($client->c2d_tags, true) ?? [];
                            // Фильтруем, удаляя нужный тег
                            $tags = array_values(array_filter($tags, fn($tag) => $tag !== (int)$webhookData['id']));

                            $this->extracted($webhookData, $tags, $client);
                            $this->checkC2dDate($client);
                        }
                        $this->clientsLog->result = "Client Tag Deleted";
                    } else {
                        $this->clientsLog->result = "Client Not Found";
                    }

                    $this->clientsLog->task_status = "done";
                    $this->clientsLog->finished_at = Carbon::now('UTC');
                    $this->clientsLog->save();
                }
            }
        } catch (Exception $e) {
            $this->clientsLog->task_status = "failed";
            $this->clientsLog->result = $e->getMessage();
            $this->clientsLog->finished_at = Carbon::now('UTC');
            $this->clientsLog->save();

            Log::error($e->getMessage());
        }
    }

    /**
     * @param mixed $webhookData
     * @param array $tags
     * @param Client|null $client
     * @return void
     */
    private function extracted(mixed $webhookData, array $tags, ?Client $client): void
    {
        $clientData = [
            'is_c2d' => 1,
            'c2d_client_id' => $webhookData['client_id'],
            'c2d_tags' => json_encode(array_values($tags)), // JSON-массив тегов
        ];

        // Добавляем c2d_channel_id, только если это add_tag_to_client
        if ($webhookData['hook_type'] == "add_tag_to_client") {
            $clientData['c2d_channel_id'] = $webhookData['channel_id'];
        }

        $client->update($clientData);

        $this->clientsLog->task_status = "done";
        $this->clientsLog->result = "Client Tags Updated";
    }

    /**
     * @param Client|null $client
     * @return void
     */
    private function checkC2dDate(?Client $client): void
    {
        if ($client->is_c2d_date == null) {
            $client->is_c2d = true;
            $client->is_c2d_date = Carbon::now('UTC');
            $client->save();
        }
    }
}
