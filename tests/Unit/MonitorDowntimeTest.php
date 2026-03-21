<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

it('logs a monitor outage when a monitored site recovers', function () {
    $this->travel(-10)->minutes();

    $monitor = MonitorFactory::new()->create([
        'url' => 'https://google.com',
        'uptime_status' => 'down',
        'uptime_check_times_failed_in_a_row' => 5,
        'uptime_status_last_change_date' => now(),
        'uptime_check_failed_event_fired_on_date' => now(),
        'uptime_check_enabled' => true,
    ]);

    $this->travelBack();

    $consecutiveFailsNeeded = config('uptime-monitor.uptime_check.fire_monitor_failed_event_after_consecutive_failures');

    foreach (range(1, $consecutiveFailsNeeded) as $index) {
        Artisan::call('monitor:check-uptime');
    }

    $this->assertDatabaseHas('monitor_outages', [
        'monitor_id' => $monitor->id,
    ]);
});
