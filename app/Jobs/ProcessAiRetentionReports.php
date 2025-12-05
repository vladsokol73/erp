<?php

namespace App\Jobs;

use App\Models\ApiToken;
use App\Models\Flag;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use OpenAI;

class ProcessAiRetentionReports implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public function handle(): void
    {
    }
}
