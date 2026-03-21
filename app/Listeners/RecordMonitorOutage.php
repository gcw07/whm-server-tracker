<?php

namespace App\Listeners;

use App\Models\MonitorOutage;
use Spatie\UptimeMonitor\Events\UptimeCheckRecovered;

class RecordMonitorOutage
{
    public function handle(UptimeCheckRecovered $event): void
    {
        MonitorOutage::create([
            'monitor_id' => $event->monitor->id,
            'started_at' => $event->downtimePeriod->startDateTime,
            'ended_at' => $event->downtimePeriod->endDateTime,
            'duration_seconds' => (int) abs($event->downtimePeriod->startDateTime->diffInSeconds($event->downtimePeriod->endDateTime)),
            'created_at' => now(),
        ]);
    }
}
