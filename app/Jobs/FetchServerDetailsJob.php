<?php

namespace App\Jobs;

use App\Models\Server;
use App\Services\WHM\WhmServerDetails;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

#[Tries(5)]
class FetchServerDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Server $server)
    {
        $this->onQueue('high');
    }

    public function handle(WhmServerDetails $whmServerDetails): void
    {
        $whmServerDetails->setServer($this->server);
        $whmServerDetails->fetch();
        dispatch(new FetchEmailDiskUsageJob($this->server));
        dispatch(new FetchAccountDetailsJob($this->server));
    }
}
