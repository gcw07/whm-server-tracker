<?php

namespace App\Jobs;

use App\Models\Server;
use App\Services\WHM\WhmApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(5)]
class FetchServerDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Server $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function handle(WhmApi $whmApi): void
    {
        $whmApi->setServer($this->server);
        $whmApi->fetch();
        dispatch(new FetchEmailDiskUsageJob($this->server))->onQueue('high');
        dispatch(new EnrichServerDataJob($this->server))->onQueue('high');
    }
}
