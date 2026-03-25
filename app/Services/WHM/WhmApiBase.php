<?php

namespace App\Services\WHM;

use App\Exceptions\Server\MissingTokenException;
use App\Models\Server;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;

abstract class WhmApiBase
{
    protected Server $server;

    public function setServer(Server $server): void
    {
        if (! $server->token) {
            throw new MissingTokenException;
        }

        $this->server = $server;
    }

    private function getHeaders(): array
    {
        $username = config('server-tracker.whm.username');

        return ['Authorization' => "whm $username:{$this->server->token}"];
    }

    protected function configuredRequest(Pool $pool, string $name): PendingRequest
    {
        return $pool->as($name)
            ->withHeaders($this->getHeaders())
            ->baseUrl($this->server->whm_base_api_url)
            ->connectTimeout(config('server-tracker.whm.connection_timeout'))
            ->withoutVerifying();
    }
}
