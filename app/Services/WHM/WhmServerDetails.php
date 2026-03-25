<?php

namespace App\Services\WHM;

use App\Events\FetchedDataFailedEvent;
use App\Events\FetchedDataSucceededEvent;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use App\Services\WHM\DataProcessors\ProcessBackups;
use App\Services\WHM\DataProcessors\ProcessDiskUsage;
use App\Services\WHM\DataProcessors\ProcessPhpInstalledVersions;
use App\Services\WHM\DataProcessors\ProcessPhpSystemVersion;
use App\Services\WHM\DataProcessors\ProcessWhmVersion;
use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class WhmServerDetails extends WhmApiBase
{
    protected array $successMessages;

    protected array $failureMessages;

    public function setServer(Server $server): void
    {
        parent::setServer($server);

        $this->successMessages = [];
        $this->failureMessages = [];
    }

    public function fetch(): void
    {
        $responses = Http::pool(fn (Pool $pool) => $this->getPoolRequests($pool));

        foreach ($responses as $type => $response) {
            if ($response instanceof \Exception) {
                $this->apiRequestFailed($type, $response->getMessage());
            } elseif ($response->failed()) {
                $this->apiRequestFailed($type, "HTTP {$response->status()} error");
            } else {
                $this->apiRequestSucceeded($type, $response->json());
            }
        }

        $this->processMessages();
    }

    protected function getPoolRequests(Pool $pool): array
    {
        return [
            'accounts' => $this->configuredRequest($pool, 'accounts')->get('listaccts?api.version=1'),
            'backups' => $this->configuredRequest($pool, 'backups')->get('backup_config_get?api.version=1'),
            'diskUsage' => $this->configuredRequest($pool, 'diskUsage')->get('getdiskusage?api.version=1'),
            'phpInstalledVersions' => $this->configuredRequest($pool, 'phpInstalledVersions')->get('php_get_installed_versions?api.version=1'),
            'phpSystemVersion' => $this->configuredRequest($pool, 'phpSystemVersion')->get('php_get_system_default_version?api.version=1'),
            'whmVersion' => $this->configuredRequest($pool, 'whmVersion')->get('version?api.version=1'),
        ];
    }

    protected function apiRequestSucceeded(string $type, array $data): void
    {
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

    protected function apiRequestFailed(string $type, string $message): void
    {
        $this->failureMessages[] = ['type' => $type, 'message' => $message];
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

        if ((int) abs($this->server->server_update_last_failed_at->diffInHours()) >= config('server-tracker.notifications.resend_failed_notification_every_hours')) {
            return true;
        }

        return false;
    }
}
