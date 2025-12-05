<?php

namespace App\Jobs;

use App\Models\clients\Client;
use App\Models\clients\ClientsLog;
use App\Models\Log;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PuzzleBot implements ShouldQueue
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
            // Обработка данных вебхука для clients
            $webhookData = json_decode($this->clientsLog->webhook_data, true); // получаем дату хука в списке

            if (isset($webhookData['c2d_channel_id'])) { // если 1 хук
                $clientData = [
                    'clickid' => $webhookData['clickid'],
                    'tg_id' => $webhookData['tg_id'],
                    'source_id' => 'PazzleBot',
                    'pb_bot_name' => $webhookData['pb_bot_name'],
                    'c2d_channel_id' => $webhookData['c2d_channel_id'],
                    'is_pb' => 1,
                ];

                if (Client::query()
                    ->where('tg_id', $webhookData['tg_id'])
                    ->where('c2d_channel_id', $webhookData['c2d_channel_id'])
                    ->exists()) // если клиент уже есть
                {

                    Client::query()
                        ->where('tg_id', $webhookData['tg_id'])
                        ->where('c2d_channel_id', $webhookData['c2d_channel_id'])
                        ->update($clientData);

                    $this->clientsLog->task_status = "done";
                    $this->clientsLog->result = "Client Updated";
                    $this->clientsLog->finished_at = Carbon::now('UTC');
                    $this->clientsLog->save();

                } else { // если нвоый клиент
                    $clientData['is_pb_date'] = $this->clientsLog->created_at;
                    FetchKeitaroData::dispatch($clientData['clickid'], $this->clientsLog, $clientData)->onQueue('high-priority');
                }


            } else { //если хук с сообщением/инфой о подписке

                if (isset($webhookData['date'])) { // если хук с сообщенькой
                    $webhookData['date'] = Carbon::createFromTimestamp($webhookData['date'])->toDateTimeString(); // преобразовывем дату в нужный формат
                    $clientData = [
                        'tg_id' => $webhookData['user']['id'],
                        'pb_last_mssg' => $webhookData['date'],
                        'pb_bot_name' => $webhookData['bot']['username'],
                    ];

                } else { // если хук подписка
                    $clientData = [
                        'tg_id' => $webhookData['tg_id'],
                        'pb_bot_name' => $webhookData['pb_bot_name'],
                        'pb_channelsub' => $webhookData['sub'],
                    ];

                    if (Client::query()
                        ->where('tg_id', $clientData['tg_id'])
                        ->where('pb_bot_name', $clientData['pb_bot_name'])
                        ->whereNull('pb_channelsub_date')
                        ->exists()) {
                        $clientData['pb_channelsub_date'] = Carbon::now('UTC'); // преобразовывем дату в нужный формат
                    }
                }

                $this->updateOrCreateClient($clientData);
                $this->clientsLog->task_status = "done";
                $this->clientsLog->result = "Client Updated";
                $this->clientsLog->finished_at = Carbon::now('UTC');
                $this->clientsLog->save();
            }
        } catch (Exception $e) {
            $this->clientsLog->task_status = "failed";
            $this->clientsLog->result = $e->getMessage();
            $this->clientsLog->finished_at = Carbon::now('UTC');
            $this->clientsLog->save();

            Log::error($e->getMessage());
        }
    }

    private function updateOrCreateClient(array $clientData): void
    {
        $clientExists = Client::query()
            ->where('tg_id', $clientData['tg_id'])
            ->where('pb_bot_name', $clientData['pb_bot_name'])
            ->exists();

        if ($clientExists) {
            // Обновляем существующего клиента
            Client::query()
                ->where('tg_id', $clientData['tg_id'])
                ->where('pb_bot_name', $clientData['pb_bot_name'])
                ->update($clientData);
        } else {
            // Логируем ошибку, если клиента нет
            $this->clientsLog->task_status = "failed";
            $this->clientsLog->finished_at = Carbon::now('UTC');
            $this->clientsLog->save();
        }
    }
}
