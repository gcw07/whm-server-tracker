<?php

namespace App\Listeners;

use App\Models\DowntimeStat;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;

class SaveDowntimeStats
{
    public function __construct()
    {
        //
    }

    public function handle(UptimeCheckRecovered $event): void
    {
        DowntimeStat::create([
            'monitor_id' => $event->monitor->id,
            'date' => today()->format('Y-m-d'),
            'downtime_period' => $event->downtimePeriod->startDateTime->diffInSeconds($event->downtimePeriod->endDateTime),
        ]);
    }
}
