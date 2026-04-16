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
Schedule::command('server-tracker:fetch-server-details')->hourly();

// Schedule Uptime Check
Schedule::command('monitor:check-uptime')->everyMinute();

// Schedule Email Blacklist Check
Schedule::command('server-tracker:check-blacklist')->dailyAt('0:30');

// Schedule WordPress Check
Schedule::command('server-tracker:check-wordpress')->dailyAt('1:15');

// Schedule Domain Name Expiration Check
Schedule::command('server-tracker:check-domain-name')->dailyAt('1:45');

// Schedule Cloudflare Zone Sync (runs after domain check so is_on_cloudflare is fresh)
Schedule::command('server-tracker:sync-cloudflare-zones')->dailyAt('2:05');

// Schedule Cloudflare Analytics Fetch (runs after zone sync at 2:05)
Schedule::command('server-tracker:fetch-cloudflare-analytics')->dailyAt('2:30');

// Schedule Lighthouse Check
Schedule::command('server-tracker:check-lighthouse')->weeklyOn(0, '3:05');
