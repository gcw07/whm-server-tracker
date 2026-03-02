<?php

namespace App\Jobs;

use App\Models\Server;
use App\Services\WHM\WhmApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchEmailDiskUsageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(public Server $server)
    {
        $this->onQueue('high');
    }

    public function handle(WhmApi $whmApi): void
    {
        $whmApi->setServer($this->server);
        $whmApi->fetchEmailDiskUsage();
    }
}
