<?php

namespace App\Observers;

use App\Models\Monitor;
use App\Models\MonitorBlacklistCheck;
use App\Models\MonitorBlacklistResult;
use App\Models\MonitorDomainCheck;
use App\Models\MonitorLighthouseCheck;
use App\Models\MonitorWordPressCheck;
use App\Services\Blacklist\BlacklistChecker;

class MonitorObserver
{
    public function created(Monitor $monitor): void
    {
        MonitorBlacklistCheck::create(['monitor_id' => $monitor->id]);

        foreach (BlacklistChecker::driverNames() as $driver) {
            MonitorBlacklistResult::create(['monitor_id' => $monitor->id, 'driver' => $driver]);
        }

        MonitorLighthouseCheck::create(['monitor_id' => $monitor->id]);
        MonitorDomainCheck::create(['monitor_id' => $monitor->id]);
        MonitorWordPressCheck::create(['monitor_id' => $monitor->id]);
    }
}
