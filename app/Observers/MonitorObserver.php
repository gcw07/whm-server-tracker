<?php

namespace App\Observers;

use App\Models\Monitor;
use App\Models\MonitorBlacklistCheck;
use App\Models\MonitorDomainCheck;
use App\Models\MonitorLighthouseCheck;

class MonitorObserver
{
    public function created(Monitor $monitor): void
    {
        MonitorBlacklistCheck::create(['monitor_id' => $monitor->id]);
        MonitorLighthouseCheck::create(['monitor_id' => $monitor->id]);
        MonitorDomainCheck::create(['monitor_id' => $monitor->id]);
    }
}
