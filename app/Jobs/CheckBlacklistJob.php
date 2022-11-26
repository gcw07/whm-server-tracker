<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\Server;
use App\Services\WHM\WhmApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckBlacklistJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Monitor $monitor;

    public int $tries = 5;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    public function handle()
    {
        $this->monitor->checkBlacklist();
    }
}
