<?php

use App\Jobs\FetchOperators;
use App\Jobs\GenerateDayStatisticsJob;
use App\Jobs\ProcessAiRetentionReports;
use Illuminate\Support\Facades\Schedule;
use \App\Jobs\Chat2DeskFreshNames;

Schedule::job(new Chat2DeskFreshNames)->everyTwoHours();

Schedule::job(new GenerateDayStatisticsJob())->hourly();

Schedule::job(new ProcessAiRetentionReports(), 'ai')
   ->dailyAt('05:00')
   ->onOneServer()
   ->withoutOverlapping();

Schedule::job(new FetchOperators)->everyTwoHours();
