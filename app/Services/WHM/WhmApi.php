<?php

namespace App\Services\WHM;

use App\Exceptions\Server\MissingTokenException;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use App\Services\WHM\DataProcessors\ProcessBackups;
use App\Services\WHM\DataProcessors\ProcessDiskUsage;
use App\Services\WHM\DataProcessors\ProcessPhpInstalledVersions;
use App\Services\WHM\DataProcessors\ProcessPhpSystemVersion;
use App\Services\WHM\DataProcessors\ProcessWhmVersion;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class WhmApi
{
    private Server $server;

    public function __construct()
    {
    }

    public function setServer(Server $server)
    {
        if (! $server->token) {
            throw new MissingTokenException;
        }

        $this->server = $server;
    }

    public function fetch()
    {
        $client = new Client([
            'base_uri' => $this->server->whm_base_api_url,
            'headers' => $this->promiseHeaders(),
            'connect_timeout' => config('server-tracker.whm.connection_timeout'),
            'verify' => false, // might remove
        ]);

        $responses = Promise\Utils::settle($this->getPromises($client))->wait();

        foreach ($responses as $type => $response) {
            if ($response['state'] === 'fulfilled') {
                $this->apiRequestSucceeded($type, $response);
            }

            if ($response['state'] === 'rejected') {
                $this->apiRequestFailed($type, $response);
            }
        }
    }

    private function promiseHeaders(): array
    {
        $username = config('server-tracker.whm.username');

        return collect([])
            ->merge(['Authorization' => "whm $username:$this->server->token"])
            ->toArray();
    }

    private function getPromises($client): array
    {
        return [
            'accounts' => $client->getAsync('listaccts?api.version=1'),
            'backups' => $client->getAsync('backup_config_get?api.version=1'),
            'diskUsage' => $client->getAsync('getdiskusage?api.version=1'),
            'phpInstalledVersions' => $client->getAsync('php_get_installed_versions?api.version=1'),
            'phpSystemVersion' => $client->getAsync('php_get_system_default_version?api.version=1'),
            'whmVersion' => $client->getAsync('version?api.version=1'),
        ];
    }

    private function apiRequestSucceeded($type, $response)
    {
        $data = json_decode($response['value']->getBody()->getContents(), true);

        match ($type) {
            'accounts' => (new ProcessAccounts)->execute($this->server, $data),
            'backups' => (new ProcessBackups)->execute($this->server, $data),
            'diskUsage' => (new ProcessDiskUsage)->execute($this->server, $data),
            'phpInstalledVersions' => (new ProcessPhpInstalledVersions)->execute($this->server, $data),
            'phpSystemVersion' => (new ProcessPhpSystemVersion)->execute($this->server, $data),
            'whmVersion' => (new ProcessWhmVersion)->execute($this->server, $data),
        };
    }

    private function apiRequestFailed($type, $response)
    {
        $data = $response['reason']->getMessage();
    }
}
