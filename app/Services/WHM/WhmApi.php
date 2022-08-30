<?php

namespace App\Services\WHM;

use App\Events\FetchedDataFailedEvent;
use App\Events\FetchedDataSucceededEvent;
use App\Exceptions\Server\MissingTokenException;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use App\Services\WHM\DataProcessors\ProcessBackups;
use App\Services\WHM\DataProcessors\ProcessDiskUsage;
use App\Services\WHM\DataProcessors\ProcessPhpInstalledVersions;
use App\Services\WHM\DataProcessors\ProcessPhpSystemVersion;
use App\Services\WHM\DataProcessors\ProcessWhmVersion;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class WhmApi
{
    protected Server $server;

    protected array $successMessages;

    protected array $failureMessages;

    public function setServer(Server $server)
    {
        if (! $server->token) {
            throw new MissingTokenException;
        }

        $this->server = $server;
        $this->successMessages = [];
        $this->failureMessages = [];
    }

    public function fetch(): void
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

        $this->processMessages();
    }

    private function promiseHeaders(): array
    {
        $username = config('server-tracker.whm.username');

        return collect([])
            ->merge(['Authorization' => "whm $username:{$this->server->token}"])
            ->toArray();
    }

    protected function getPromises($client): array
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

    protected function apiRequestSucceeded($type, $response): void
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

        $this->successMessages[] = ['type' => $type, 'message' => 'success'];
    }

    protected function apiRequestFailed($type, $response): void
    {
        $this->failureMessages[] = ['type' => $type, 'message' => $response['reason']->getMessage()];
    }

    protected function processMessages(): void
    {
        if (count($this->successMessages) > 0) {
            $this->server->update([
                'server_update_last_succeeded_at' => Carbon::now(),
                'server_update_last_failed_at' => null,
            ]);

            event(new FetchedDataSucceededEvent($this->server, $this->successMessages));
        }

        if (count($this->failureMessages) > 0 && $this->shouldFireFailedEvent()) {
            $this->server->update([
                'server_update_last_failed_at' => Carbon::now(),
            ]);

            event(new FetchedDataFailedEvent($this->server, $this->failureMessages));
        }
    }

    protected function shouldFireFailedEvent(): bool
    {
        if (is_null($this->server->server_update_last_failed_at)) {
            return true;
        }

        if ($this->server->server_update_last_failed_at->diffInHours() >= config('server-tracker.notifications.resend_failed_notification_every_hours')) {
            return true;
        }

        return false;
    }
}
