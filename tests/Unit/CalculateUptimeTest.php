<?php

use App\Models\Monitor;
use App\Models\MonitorOutage;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

it('calculates uptime percentage over a date span', function () {
    MonitorFactory::new()->create([
        'url' => 'https://mysite.com',
    ]);

    $monitor = Monitor::first();

    $startDate = today()->subDays(6);
    $endDate = today();

    // Create one 120-second outage per day for all 7 days
    for ($i = 6; $i >= 0; $i--) {
        $outageStart = today()->subDays($i)->setTime(2, 0, 0);
        MonitorOutage::factory()->create([
            'monitor_id' => $monitor->id,
            'started_at' => $outageStart,
            'ended_at' => $outageStart->copy()->addSeconds(120),
            'duration_seconds' => 120,
        ]);
    }

    // Add a second 120-second outage on today only
    $todayStart = today()->setTime(3, 0, 0);
    MonitorOutage::factory()->create([
        'monitor_id' => $monitor->id,
        'started_at' => $todayStart,
        'ended_at' => $todayStart->copy()->addSeconds(120),
        'duration_seconds' => 120,
    ]);

    // Total downtime: 6 days * 120s + 1 day * 240s = 720 + 240 = 960s
    // Total possible: 7 * 86400 = 604800s
    // Uptime = 100 - (960 / 604800 * 100) = 100 - 0.158... = 99.84%
    tap($monitor->fresh(), function ($monitor) {
        $this->assertEquals(99.84, $monitor->uptime_for_last_seven_days);
    });
});
