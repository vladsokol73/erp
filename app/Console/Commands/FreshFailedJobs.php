<?php

namespace App\Console\Commands;

use App\Jobs\Chat2Desk;
use App\Models\clients\Client;
use App\Models\clients\ClientsLog;
use App\Models\ProductLog;
use DateTime;
use DateTimeZone;
use Illuminate\Console\Command;

class FreshFailedJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fresh-clients-c2d';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Starting fresh jobs");

        $count = 0;
        $clientCount = 0;
        $clientLogs = ClientsLog::whereBetween('created_at', ['2025-03-13 00:00:00', '2025-04-20 23:59:59'])
            ->where(function ($query) {
                $query->whereJsonContains('webhook_data->hook_type', 'new_client');
            })
            ->get();

        $this->info(count($clientLogs) . ' clients found');

        foreach ($clientLogs as $log) {
            $webhookData = json_decode($log->webhook_data, true);
            switch ($webhookData['hook_type']) {
                case 'inbox':
                    $log->c2d_client_id = $webhookData['client']['id'];
                    $log->save();
                    $this->info("Updated inbox hook");
                    $client = Client::where('tg_id', $log->client_id)
                        ->where('c2d_channel_id', $webhookData['channel_id'])
                        ->first();
                    if ($client) {
                        $client->c2d_client_id = $log->c2d_client_id;
                        $client->save();
                        $this->info("Updated client with id: $client->id");
                        $clientCount++;
                    }
                    break;
                case 'new_client':
                    $log->c2d_client_id = $webhookData['id'];
                    $log->save();
                    $this->info("Updated new_client hook");
                    $count ++;
                    $channels = $webhookData['channels'];
                    $channelIds = collect($channels)->pluck('id')->all();
                    $client = Client::where('tg_id', $log->client_id)
                        ->whereIn('c2d_channel_id', $channelIds)
                        ->first();
                    if ($client) {
                        if (!$client->c2d_client_id) {
                            $client->c2d_client_id = $log->c2d_client_id;
                            $client->save();
                            $this->info("Updated client with id: $client->id");
                            $clientCount++;
                        }
                    }
                    break;
                case 'add_tag_to_client':
                case 'delete_tag_from_client':
                    $log->c2d_client_id = $webhookData['client_id'];
                    $log->save();
                    $this->info("Updated tag hook");
                    break;
            }
        }

        $this->info("Updated $count logs. \n And Updated $clientCount clients.");
    }
}
