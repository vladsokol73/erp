<?php

namespace App\Console\Commands;

use App\Jobs\Chat2Desk;
use App\Models\Client\Client;
use App\Models\Client\ClientsLog;
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
        $clients = Client::whereBetween('is_c2d_date', [
            now()->startOfMonth()->toDateString(),
            now()->toDateString()
        ])->get();

        foreach ($clients as $client) {
            $log = ClientsLog::query()
                ->where('client_id', $client->tg_id)
                ->whereJsonContains('webhook_data->channel_id', (int)$client->c2d_channel_id)
                ->where(function ($query) {
                    $query->whereJsonContains('webhook_data->hook_type', 'inbox')
                        ->orWhereJsonContains('webhook_data->hook_type', 'add_tag_to_client')
                        ->orWhereNotNull('webhook_data->clickid');
                })
                ->orderBy('id')
                ->first();

            if ($log) {
                $time = $log->created_at;
                // Обновляем клиента
                $client->update([
                    'is_c2d_date' => $time,
                    'is_c2d' => true,
                    ]);

                $count++;
                $this->info("Updated client ID: {$client->id}, new is_c2d_date: {$time}");
            } else {
                $client->update([
                    'is_c2d_date' => null,
                    'is_c2d' => false,
                ]);
                $count++;
                $this->info("Updated client ID: {$client->id} C2D Deleted");
            }
        }

        $this->info("Updated $count clients.");
    }
}
