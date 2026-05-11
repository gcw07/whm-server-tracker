<?php

use App\Console\Commands\CheckBlacklistCommand;
use App\Console\Commands\CheckDomainNameCommand;
use App\Console\Commands\CheckLighthouseCommand;
use App\Console\Commands\CheckWordPressCommand;
use App\Console\Commands\FetchCloudflareAnalyticsCommand;
use App\Console\Commands\FetchServerDetailsCommand;
use App\Console\Commands\SyncCloudflareZonesCommand;
use App\Models\Monitor;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule Horizon Snapshots
Schedule::command('horizon:snapshot')->everyFiveMinutes();

// Schedule Server Tracker
Schedule::command(FetchServerDetailsCommand::class)->hourly();

// Schedule Uptime Check
Schedule::command('monitor:check-uptime')->everyMinute();

// Prune soft-deleted monitors older than 7 days
Schedule::command('model:prune', ['--model' => [Monitor::class]])->dailyAt('0:15');

// Schedule Email Blacklist Check
Schedule::command(CheckBlacklistCommand::class)->dailyAt('0:30');

// Schedule WordPress Check
Schedule::command(CheckWordPressCommand::class)->dailyAt('1:15');

// Schedule Domain Name Expiration Check
Schedule::command(CheckDomainNameCommand::class)->dailyAt('1:45');

// Schedule Cloudflare Zone Sync (runs after domain check so is_on_cloudflare is fresh)
Schedule::command(SyncCloudflareZonesCommand::class)->dailyAt('2:05');

// Schedule Cloudflare Analytics Fetch (runs after zone sync at 2:05)
Schedule::command(FetchCloudflareAnalyticsCommand::class)->dailyAt('2:30');

// Schedule Lighthouse Check
Schedule::command(CheckLighthouseCommand::class)->weeklyOn(0, '3:05');
