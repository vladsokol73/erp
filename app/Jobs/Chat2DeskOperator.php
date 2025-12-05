<?php

namespace App\Jobs;

use App\Models\Operator\Channel;
use App\Models\Operator\Operator;
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

    public function handle(): void
    {
        Operator::firstOrCreate([
            'operator_id' => $this->operatorLog->operator_id,
            ]);

        Channel::firstOrCreate([
            'channel_id' => $this->operatorLog->channel_id,
            ]);
    }
}
