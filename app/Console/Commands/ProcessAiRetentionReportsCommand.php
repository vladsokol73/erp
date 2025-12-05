<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAiRetentionReports;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class ProcessAiRetentionReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai-retention:process {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch AI retention reports job for a specific date (format: Y-m-d). If date is not provided, uses yesterday.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $dateInput = $this->argument('date');
        
        if ($dateInput) {
            // Валидация формата даты
            $validator = Validator::make(
                ['date' => $dateInput],
                ['date' => 'required|date_format:Y-m-d']
            );

            if ($validator->fails()) {
                $this->error('Invalid date format. Use Y-m-d format (e.g., 2025-01-15)');
                return;
            }

            try {
                ProcessAiRetentionReports::dispatch($dateInput);
                $this->info("AI retention reports job dispatched for date: {$dateInput}");
            } catch (Exception $e) {
                $this->error("Failed to dispatch job: " . $e->getMessage());
            }
        } else {
            try {
                ProcessAiRetentionReports::dispatch();
                $this->info("AI retention reports job dispatched for yesterday's date");
            } catch (Exception $e) {
                $this->error("Failed to dispatch job: " . $e->getMessage());
            }
        }
    }
}

