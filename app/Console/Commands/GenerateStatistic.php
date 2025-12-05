<?php

namespace App\Console\Commands;

use App\Jobs\GenerateDayStatisticsJob;
use Exception;
use Illuminate\Console\Command;

class GenerateStatistic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:operator-statistic {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if ($this->argument('date')) {
            $date = $this->argument('date');
            try {
                GenerateDayStatisticsJob::dispatch($date);
                $this->info("Start generate statistic job with date $date");
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }

        } else {
            try {
                GenerateDayStatisticsJob::dispatch();
                $this->info("Start generate statistic job");
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }
}
