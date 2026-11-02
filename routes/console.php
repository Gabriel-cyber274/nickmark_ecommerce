<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the new product notification command to run daily at 10:00 AM
Schedule::command('products:notify-new --days=1')
    ->dailyAt('10:00')
    ->description('Send daily new product notifications to all users');

// You can also schedule other tasks here:
// Schedule::command('products:notify-new --days=7')
//     ->weeklyOn(1, '09:00') // Every Monday at 9:00 AM
//     ->description('Send weekly new product digest');