<?php

namespace App\Events;

use App\Models\Monitor;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;

class DomainNameExpiresSoonEvent implements ShouldQueue
{
    public Monitor $monitor;

    public Carbon $date;

    public function __construct(Monitor $monitor, Carbon $date)
    {
        $this->monitor = $monitor;

        $this->date = $date;
    }
}
