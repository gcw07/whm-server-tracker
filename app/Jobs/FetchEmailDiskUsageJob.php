<?php

namespace App\Jobs;

use App\Models\Server;
use App\Services\WHM\WhmEmailDiskUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(5)]
class FetchEmailDiskUsageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Server $server)
    {
        $this->onQueue('high');
    }

    public function handle(WhmEmailDiskUsage $whmEmailDiskUsage): void
    {
        $whmEmailDiskUsage->setServer($this->server);
        $whmEmailDiskUsage->fetch();
    }
}
