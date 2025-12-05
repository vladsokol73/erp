<?php

namespace App\Jobs;

use App\Models\Operator\Channel;
use App\Models\Operator\Operator;
use App\Services\FlagService;
use App\Services\Operator\AiRetentionReportGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessAiRetentionReports implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, Queueable, SerializesModels;

    /**
     * Увеличиваем таймаут джоба (в секундах) — до 30 минут
     * Управляет временем выполнения на стороне очереди
     */
    public $timeout = 3600;

    /**
     * Prevent duplicate dispatches while a run is in progress (seconds).
     */
    public $uniqueFor = 7200;

    /**
     * Дата для обработки (формат Y-m-d). Если null, используется вчерашний день.
     */
    public function __construct(
        public ?string $date = null
    ) {
        $this->onQueue('ai');
    }

    public function handle(): void
    {
        $flagService = app(FlagService::class);
        $generator = app(AiRetentionReportGenerator::class);

        $operators = $flagService
            ->getModelsWithFlag(Operator::class, 'ai_retention')
            ->pluck('operator_id')
            ->all();

        $channels = $flagService
            ->getModelsWithFlag(Channel::class, 'ai_retention')
            ->pluck('channel_id')
            ->all();

        $generator->generate(
            operatorIds: $operators,
            channelIds: $channels,
            date: $this->date,
            save: true
        );
    }
}
