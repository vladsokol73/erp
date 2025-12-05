<?php

namespace App\Jobs;

use App\Models\clients\ClientsLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Chat2Desk implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected $clientsLog;

    /**
     * Create a new job instance.
     */
    public function __construct(ClientsLog $clientsLog)
    {
        $this->clientsLog = $clientsLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Пустая реализация - вся логика будет в основном приложении
    }
}
