<?php

namespace App\Jobs;

use App\Models\Server;
use App\Services\WHM\WhmApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchServerDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Server $server;

    public int $tries = 5;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function handle(WhmApi $whmApi): void
    {
        $whmApi->setServer($this->server);
        $whmApi->fetch();
    }
}
