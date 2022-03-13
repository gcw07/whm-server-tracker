<?php

namespace App\Services\WHM;

use App\Exceptions\Server\MissingTokenException;
use App\Models\Server;
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
            'accounts' => $this->processAccounts($data),
            'backups' => $this->processBackups($data),
            'diskUsage' => $this->processDiskUsage($data),
            'phpInstalledVersions' => $this->processPhpInstalledVersions($data),
            'phpSystemVersion' => $this->processPhpSystemVersion($data),
            'whmVersion' => $this->processWhmVersion($data),
        };
    }

    private function apiRequestFailed($type, $response)
    {
        $data = $response['reason']->getMessage();
    }

    public function processAccounts($data)
    {
        $data['data']['acct'];
    }

    public function processBackups($data)
    {
        $backups = $data['data']['backup_config'];

        $this->server->settings()->merge([
            'backup_enabled' => $backups['backupenable'],
            'backup_days' => $backups['backupdays'],
            'backup_retention' => $backups['backup_daily_retention'],
        ]);
    }

    public function processDiskUsage($data)
    {
        $partition = $this->findPrimaryPartition($data['data']['partition']);
    }

    public function processPhpInstalledVersions($data)
    {
        $data['data']['versions'];
    }

    public function processPhpSystemVersion($data)
    {
        $data['data']['version'];
    }

    public function processWhmVersion($data)
    {
        $data['data']['version'];
    }

    private function findPrimaryPartition($partitions): string
    {
        if (sizeof($partitions) > 1) {
            return collect($partitions)->firstWhere('mount', '/');
        }

        return $partitions[0];
    }
}
