<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Services\Blacklist\BlacklistChecker;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(5)]
class CheckBlacklistJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Monitor $monitor) {}

    public function handle(BlacklistChecker $checker): void
    {
        $checker->check($this->monitor);
    }
}
