<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;
use Spatie\UptimeMonitor\Helpers\Period;

uses(LazilyRefreshDatabase::class);

it('logs a downtime stat when a monitored site recovers', function () {
    $this->travel(-4)->minutes();

    $monitor = MonitorFactory::new()->create([
        'url' => 'https://this-site-is-not-real.co',
        'uptime_status' => 'down',
        'uptime_status_last_change_date' => now(),
        'uptime_check_enabled' => true,
    ]);

    $this->travelBack();

    event(new UptimeCheckRecovered($monitor, new Period(
        $monitor->uptime_status_last_change_date,
        now()
    )));

    $this->assertDatabaseHas('downtime_stats', [
        'downtime_period' => '240',
    ]);
});
