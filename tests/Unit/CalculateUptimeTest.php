<?php

use App\Models\DowntimeStat;
use App\Models\Monitor;
use Carbon\CarbonPeriod;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

it('calculates uptime percentage over a date span', function () {
    MonitorFactory::new()->create([
        'url' => 'https://mysite.com',
    ]);

    $monitor = Monitor::first();

    $startDate = today()->subDays(6)->format('Y-m-d');
    $endDate = today()->format('Y-m-d');

    $dates = CarbonPeriod::create($startDate, '1 day', $endDate);

    // 99.86% uptime 6 of 7 days
    foreach ($dates as $date) {
        DowntimeStat::factory()->create([
            'monitor_id' => $monitor->id,
            'date' => $date->format('Y-m-d'),
            'downtime_period' => 120, // 2 minutes
        ]);
    }

    // 99.72% 1 of 7 days
    DowntimeStat::factory()->create([
        'monitor_id' => $monitor->id,
        'date' => today()->format('Y-m-d'),
        'downtime_period' => 120, // 2 minutes
    ]);

    tap($monitor->fresh(), function ($monitor) {
        $this->assertEquals(99.84, $monitor->uptime_for_last_seven_days);
    });
});
