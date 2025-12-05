<?php

namespace App\Jobs;

use App\Models\clients\Client;
use App\Models\clients\ClientsLog;
use App\Models\Log;
use App\Models\Operator\OperatorLog;
use App\Models\ProductLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Product implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected $clientsLog;

    public function __construct(ClientsLog $clientsLog)
    {
        $this->clientsLog = $clientsLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Статус в процессе
        $this->clientsLog->task_status = "in_progress";
        $this->clientsLog->started_at = Carbon::now('UTC');
        $this->clientsLog->save();

        try {
            $webhookData = json_decode($this->clientsLog->webhook_data, true); // получаем дату хука в списке

            if ($webhookData['sub4'] && $webhookData['sub5']) {
                $client = Client::query()->where('tg_id', $webhookData['sub5'])->where('c2d_channel_id', $webhookData['sub4'])->first();
            } else {
                $client = null;
            }


            $productData = [
                'player_id' => $webhookData['player_id'],
                'prod_id' => $webhookData['prod_id'],
                'tg_id' => $webhookData['sub5'],
                'c2d_channel_id' => $webhookData['sub4'],
                'status' => $webhookData['status']
            ];

            if (isset($webhookData['currency'])) {
                $productData['currency'] = $webhookData['currency'];
            }

            if (isset($webhookData['sum']) && is_numeric($webhookData['sum'])) {
                $productData['dep_sum'] = $webhookData['sum'];
            } else {
                $productData['dep_sum'] = null;
            }

            if ($client) {
                $updateData = [
                    'player_id' => $webhookData['player_id'],
                    'prod_id' => $webhookData['prod_id'],
                ];

                if (isset($webhookData['currency'])) {
                    $updateData['currency'] = $webhookData['currency'];
                }

                if ($webhookData['status'] == 'reg') {
                    $updateData += [
                        'reg' => 1,
                        'reg_date' => Carbon::now('UTC')
                    ];

                } else if ($webhookData['status'] == 'fd') {
                    $updateData += [
                        'dep' => 1,
                        'dep_date' => Carbon::now('UTC'),
                        'dep_sum' => $client->dep_sum + $webhookData['sum']
                    ];


                } else if ($webhookData['status'] == 'fd_r') {
                    $updateData['redep'] = 1;
                    $updateData['redep_date'] = Carbon::now('UTC');
                    $updateData['dep_sum'] = $client->dep_sum + $webhookData['sum'];

                    if ($client->dep == 0) {
                        $updateData['dep'] = 1;
                        $updateData['dep_date'] = Carbon::now('UTC');
                    }
                } else if ($webhookData['status'] == 'fd_a') {
                    $updateData['fd_a'] = 1;
                }

                // Обновляем только непустые поля
                $client->update($updateData);

                $productData['operator_id'] = OperatorLog::where('client_id', $webhookData['sub5'])
                    ->latest('event_time')
                    ->first()
                    ->operator_id;

                $this->clientsLog->task_status = "done";
                $this->clientsLog->result = 'Client Updated';
            } else {
                $this->clientsLog->task_status = "done";
                $this->clientsLog->result = 'Client Not Found';
            }

            ProductLog::query()->create($productData);

            $this->clientsLog->finished_at = Carbon::now('UTC');
            $this->clientsLog->save();
        } catch (Exception $e) {
            $this->clientsLog->task_status = "failed";
            $this->clientsLog->result = $e->getMessage();
            $this->clientsLog->finished_at = Carbon::now('UTC');
            $this->clientsLog->save();

            Log::error($e->getMessage());
        }
    }
}
