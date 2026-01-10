<?php

use App\Jobs\GenerateWeeklyReportsJob;
use App\Jobs\ScheduledCrawlJob;
use App\Jobs\SendApprovedReportsJob;
use Illuminate\Support\Facades\Schedule;

// Run scheduled crawls every 15 minutes
Schedule::job(new ScheduledCrawlJob)->everyFifteenMinutes();

// Generate weekly reports every Monday at 8am
Schedule::job(new GenerateWeeklyReportsJob)->weeklyOn(1, '08:00');

// Send approved reports every hour
Schedule::job(new SendApprovedReportsJob)->hourly();
