<?php

namespace App\Jobs;

use App\Models\clients\ClientsLog;
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

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    }
}
