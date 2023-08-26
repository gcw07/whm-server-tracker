<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Schedule Horizon Snapshots
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        // Schedule Server Tracker
        $schedule->command('server-tracker:refresh')->hourly();

        // Schedule Uptime Check
        $schedule->command('monitor:check-uptime')->everyMinute();

        // Schedule SSL Certificate Check
        $schedule->command('monitor:check-certificate')->daily();

        // Schedule Email Blacklist Check
        $schedule->command('server-tracker:check-blacklist')->dailyAt('0:30');

        // Schedule WordPress Check
        $schedule->command('server-tracker:check-wordpress')->dailyAt('1:15');

        // Schedule Domain Name Expiration Check
        $schedule->command('server-tracker:check-domain-name')->dailyAt('2:00');

        // Schedule Lighthouse Check
        $schedule->command('server-tracker:check-lighthouse')->dailyAt('3:10');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
