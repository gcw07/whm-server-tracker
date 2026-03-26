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
        $responses = Http::pool(fn (Pool $pool) => $this->getPoolRequests($pool));

        foreach ($responses as $type => $response) {
            if ($response instanceof \Exception) {
                $this->requestFailed($type, $response->getMessage());
            } elseif ($response->failed()) {
                $this->requestFailed($type, "HTTP {$response->status()} error");
            } else {
                $this->requestSucceeded($type, $response->json());
            }
        }
    }

    protected function getPoolRequests(Pool $pool): array
    {
        return [
            'sslVhosts' => $this->configuredRequest($pool, 'sslVhosts')->get('fetch_ssl_vhosts?api.version=1'),
            'phpVhostVersions' => $this->configuredRequest($pool, 'phpVhostVersions')->get('php_get_vhost_versions?api.version=1'),
        ];
    }

    protected function requestSucceeded(string $type, array $data): void
    {
        match ($type) {
            'sslVhosts' => (new ProcessSslVhosts)->execute($this->server, $data),
            'phpVhostVersions' => (new ProcessPhpVhostVersions)->execute($this->server, $data),
        };
    }

    protected function requestFailed(string $type, string $message): void {}
}
