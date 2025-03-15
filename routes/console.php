<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Horizon Snapshots
Schedule::command('horizon:snapshot')->everyFiveMinutes();

// Schedule Server Tracker
Schedule::command('server-tracker:refresh')->hourly();

// Schedule Uptime Check
Schedule::command('monitor:check-uptime')->everyMinute();

// Schedule SSL Certificate Check
Schedule::command('monitor:check-certificate')->daily();

// Schedule Email Blacklist Check
Schedule::command('server-tracker:check-blacklist')->dailyAt('0:30');

// Schedule WordPress Check
Schedule::command('server-tracker:check-wordpress')->dailyAt('1:15');

// Schedule Domain Name Expiration Check
Schedule::command('server-tracker:check-domain-name')->dailyAt('2:00');

// Schedule Lighthouse Check
Schedule::command('server-tracker:check-lighthouse')->dailyAt('3:10');
