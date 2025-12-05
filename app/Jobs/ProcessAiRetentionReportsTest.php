<?php

namespace App\Jobs;

use App\Models\Operator\Channel;
use App\Models\Operator\Operator;
use App\Services\FlagService;
use App\Services\Operator\AiRetentionReportGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessAiRetentionReportsTest implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function __construct(
        public int $limit = 5,
        public ?int $userId = null,
    ) {}

    public function handle(): void
    {
    }
}


