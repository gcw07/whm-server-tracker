<?php

namespace App\Services\WHM;

use App\Events\FetchedDataFailedEvent;
use App\Events\FetchedDataSucceededEvent;
use App\Exceptions\Server\MissingTokenException;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccountEmails;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use App\Services\WHM\DataProcessors\ProcessBackups;
use App\Services\WHM\DataProcessors\ProcessDiskUsage;
use App\Services\WHM\DataProcessors\ProcessPhpInstalledVersions;
use App\Services\WHM\DataProcessors\ProcessPhpSystemVersion;
use App\Services\WHM\DataProcessors\ProcessWhmVersion;
use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

class WhmApi
{
    protected Server $server;

    protected array $successMessages;

    protected array $failureMessages;

    public function setServer(Server $server): void
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

    private function getHeaders(): array
    {
        $username = config('server-tracker.whm.username');

        return ['Authorization' => "whm $username:{$this->server->token}"];
    }

    private function configuredRequest(Pool $pool, string $name): \Illuminate\Http\Client\PendingRequest
    {
        return $pool->as($name)
            ->withHeaders($this->getHeaders())
            ->baseUrl($this->server->whm_base_api_url)
            ->connectTimeout(config('server-tracker.whm.connection_timeout'))
            ->withoutVerifying();
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

    public function fetchEmailDiskUsage(): void
    {
        $accounts = $this->server->accounts()->get();

        if ($accounts->isEmpty()) {
            return;
        }

        $responses = Http::pool(fn (Pool $pool) => $accounts->flatMap(fn ($account) => [
            $this->configuredRequest($pool, $account->user)
                ->get('cpanel', [
                    'cpanel_jsonapi_user' => $account->user,
                    'cpanel_jsonapi_module' => 'Email',
                    'cpanel_jsonapi_func' => 'list_pops_with_disk',
                    'cpanel_jsonapi_apiversion' => 3,
                ]),
            $this->configuredRequest($pool, "{$account->user}_system")
                ->get('cpanel', [
                    'cpanel_jsonapi_user' => $account->user,
                    'cpanel_jsonapi_module' => 'Email',
                    'cpanel_jsonapi_func' => 'get_main_account_disk_usage_bytes',
                    'cpanel_jsonapi_apiversion' => 3,
                ]),
        ])->all());

        foreach ($accounts as $account) {
            $emailsResponse = $responses[$account->user] ?? null;
            $systemResponse = $responses["{$account->user}_system"] ?? null;

            if (! $emailsResponse || $emailsResponse instanceof \Exception || $emailsResponse->failed()) {
                continue;
            }

            $emailData = $emailsResponse->json();

            if ($systemResponse && ! ($systemResponse instanceof \Exception) && ! $systemResponse->failed()) {
                $systemBytes = $systemResponse->json()['result']['data'] ?? 0;
                $emailData['result']['data'][] = [
                    'email' => $account->user,
                    'user' => 'system',
                    'domain' => $account->domain,
                    '_diskused' => $systemBytes,
                    '_diskquota' => 0,
                    'diskusedpercent_float' => 0,
                    'suspended_incoming' => 0,
                    'suspended_login' => 0,
                ];
            }

            (new ProcessAccountEmails)->execute($account, $emailData);
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
