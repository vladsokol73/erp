<?php

namespace App\Jobs;

use App\Models\Client\Client;
use App\Models\Client\ClientsLog;
use App\Providers\GuzzleClientProvider;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class FetchKeitaroData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Добавлен Dispatchable

    protected $clickid;
    protected $clientsLog;
    protected $clientData;

    public function __construct($clickid, ClientsLog $clientsLog, $clientData)
    {
        $this->clickid = $clickid;
        $this->clientsLog = $clientsLog;
        $this->clientData = $clientData;
    }

    public function handle()
    {
        $client = GuzzleClientProvider::getInstance();
        $clientData = $this->clientData;


        $response = $client->request('POST', Config::get('app.keitaro_endpoint'), [
            'headers' => [
                'Api-Key' => env('KEITARO_KEY'),
                'Accept' => 'application/json',
            ],
            'json' => [
                'range' => [
                    'interval' => 'all_time',
                    'timezone' => 'Europe/Moscow'
                ],
                'columns' => [
                    'country_code', 'language', 'device_type', 'user_agent', 'os', 'os_version',
                    'device_model', 'browser', 'ip', 'sub_id_1', 'sub_id_2', 'sub_id_3',
                    'sub_id_4', 'sub_id_5', 'sub_id_6', 'sub_id_7', 'sub_id_8', 'sub_id_9',
                    'sub_id_10', 'sub_id_11', 'sub_id_12', 'sub_id_13', 'sub_id_14', 'sub_id_15'
                ],
                'filters' => [
                    [
                        'name' => 'sub_id',
                        'operator' => 'EQUALS',
                        'expression' => $this->clickid
                    ]
                ],
                'sort' => [
                    [
                        'name' => 'datetime',
                        'order' => 'asc'
                    ]
                ],
                'limit' => 1
            ],
            'verify' => false
        ]);

        $data = json_decode($response->getBody(), true);

        if (!!$data['rows']) {
            // Данные, которые нужно получить
            $fields = [
                'geo_click', 'lang', 'type', 'user_agent', 'oc', 'ver_oc', 'model',
                'browser', 'ip', 'sub1', 'sub2', 'sub3', 'sub4', 'sub5', 'sub6',
                'sub7', 'sub8', 'sub9', 'sub10', 'sub11', 'sub12', 'sub13', 'sub14', 'sub15'
            ];
            // данные, которые нам приходят
            $columns = [
                'country_code', 'language', 'device_type', 'user_agent', 'os', 'os_version',
                'device_model', 'browser', 'ip', 'sub_id_1', 'sub_id_2', 'sub_id_3',
                'sub_id_4', 'sub_id_5', 'sub_id_6', 'sub_id_7', 'sub_id_8', 'sub_id_9',
                'sub_id_10', 'sub_id_11', 'sub_id_12', 'sub_id_13', 'sub_id_14', 'sub_id_15'
            ];

            $counter = 0;
            foreach ($columns as $column) {
                $clientData[$fields[$counter]] = $data['rows']['0'][$column];
                $counter++;
            }
        }
        if (!Client::query()
            ->where('tg_id', $clientData['tg_id'])
            ->where('c2d_channel_id', $clientData['c2d_channel_id'])
            ->exists()) {
            Client::query()->create($clientData);
        }

        $this->clientsLog->task_status = "done";
        $this->clientsLog->finished_at = Carbon::now('UTC');
        $this->clientsLog->result = "Client Created";
        $this->clientsLog->save();
    }
}
