<?php

namespace App\Jobs;

use App\Models\Operator\OperatorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Chat2DeskOperator implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels, Dispatchable;

    protected $operatorLog;

    public function __construct(OperatorLog $operatorLog)
    {
        $this->operatorLog = $operatorLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Пустая реализация - вся логика будет в основном приложении
    }
}
