<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
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

        // Schedule Lighthouse Check
        $schedule->command('server-tracker:check-lighthouse')->weeklyOn(1, '3:10');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
