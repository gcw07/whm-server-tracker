<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckLighthouseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Monitor $monitor;

    public int $tries = 5;

    public function __construct(Monitor $monitor)
    {
        $this->monitor = $monitor;
    }

    public function handle(): void
    {
        $this->monitor->checkLighthouse();
    }
}
