<?php

namespace App\Services\WHM;

use App\Services\WHM\DataProcessors\ProcessPhpVhostVersions;
use App\Services\WHM\DataProcessors\ProcessSslVhosts;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class WhmAccountDetails extends WhmApiBase
{
    public function fetch(): void
    {
        $responses = Http::pool(fn (Pool $pool) => [
            'sslVhosts' => $this->configuredRequest($pool, 'sslVhosts')->get('fetch_ssl_vhosts?api.version=1'),
            'phpVhostVersions' => $this->configuredRequest($pool, 'phpVhostVersions')->get('php_get_vhost_versions?api.version=1'),
        ]);

        $sslResponse = $responses['sslVhosts'] ?? null;
        $phpResponse = $responses['phpVhostVersions'] ?? null;

        if ($sslResponse && ! ($sslResponse instanceof \Exception) && ! $sslResponse->failed()) {
            (new ProcessSslVhosts)->execute($this->server, $sslResponse->json());
        }

        if ($phpResponse && ! ($phpResponse instanceof \Exception) && ! $phpResponse->failed()) {
            (new ProcessPhpVhostVersions)->execute($this->server, $phpResponse->json());
        }
    }
}
