<?php

use App\Console\Commands\NotifyEndedFeeds;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(NotifyEndedFeeds::class)->everyFiveMinutes();

// Add a new scheduled task to force send notifications once a day
Schedule::command(NotifyEndedFeeds::class.' --force')->daily();
