<?php

namespace App\Events;

use App\Models\Server;
use Illuminate\Contracts\Queue\ShouldQueue;

class FetchedDataFailedEvent implements ShouldQueue
{
    public Server $server;

    public array $messages;

    public function __construct(Server $server, array $messages)
    {
        $this->server = $server;

        $this->messages = $messages;
    }
}
