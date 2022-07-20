<?php

namespace Tests\Factories;

use App\Exceptions\Server\MissingTokenException;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use App\Services\WHM\DataProcessors\ProcessBackups;
use App\Services\WHM\DataProcessors\ProcessDiskUsage;
use App\Services\WHM\DataProcessors\ProcessPhpInstalledVersions;
use App\Services\WHM\DataProcessors\ProcessPhpSystemVersion;
use App\Services\WHM\DataProcessors\ProcessWhmVersion;
use App\Services\WHM\WhmApi;

class WhmApiFake extends WhmApi
{
    protected Server $server;

    public function setServer(Server $server)
    {
        if (! $server->token) {
            throw new MissingTokenException;
        }

        $this->server = $server;
    }

    public function fetch(): void
    {
        $this->apiRequestSucceeded('accounts', $this->getAccountsData());
        $this->apiRequestSucceeded('backups', $this->getBackupsData());
        $this->apiRequestSucceeded('diskUsage', $this->getDiskUsageData());
        $this->apiRequestSucceeded('phpInstalledVersions', $this->getPhpInstalledVersionsData());
        $this->apiRequestSucceeded('phpSystemVersion', $this->getPhpSystemVersionData());
        $this->apiRequestSucceeded('whmVersion', $this->getWhmVersionData());
    }

    protected function apiRequestSucceeded($type, $data): void
    {
        match ($type) {
            'accounts' => (new ProcessAccounts)->execute($this->server, $data),
            'backups' => (new ProcessBackups)->execute($this->server, $data),
            'diskUsage' => (new ProcessDiskUsage)->execute($this->server, $data),
            'phpInstalledVersions' => (new ProcessPhpInstalledVersions)->execute($this->server, $data),
            'phpSystemVersion' => (new ProcessPhpSystemVersion)->execute($this->server, $data),
            'whmVersion' => (new ProcessWhmVersion)->execute($this->server, $data),
        };
    }

    protected function getAccountsData(): array
    {
        return [
            'data' => [
                'acct' => [
                    [
                        'backup' => 0,
                        'diskused' => '300M',
                        'disklimit' => '2000M',
                        'domain' => 'my-site.com',
                        'ip' => '192.168.0.128',
                        'plan' => '2 Gig',
                        'startdate' => '13 May 22 16:03',
                        'suspended' => 0,
                        'suspendreason' => 'not suspended',
                        'suspendtime' => 1594040856,
                        'user' => 'mysite',
                    ],
                    [
                        'backup' => 0,
                        'diskused' => '300M',
                        'disklimit' => '2000M',
                        'domain' => 'super-system.com',
                        'ip' => '1.1.1.1',
                        'plan' => '2 Gig',
                        'startdate' => '13 May 22 16:03',
                        'suspended' => 0,
                        'suspendreason' => 'not suspended',
                        'suspendtime' => 1594040856,
                        'user' => 'super',
                    ],
                ],
            ],
        ];
    }

    protected function getBackupsData(): array
    {
        return [
            'data' => [
                'backup_config' => [
                    'backupenable' => 1,
                    'backup_daily_enable' => 1,
                    'backup_daily_retention' => 10,
                    'backupdays' => '0,2,4,6',
                    'backup_weekly_enable' => 1,
                    'backup_weekly_retention' => 10,
                    'backup_weekly_day' => '2',
                    'backup_monthly_enable' => 1,
                    'backup_monthly_retention' => 3,
                    'backup_monthly_dates' => '1,15',
                ],
            ],
        ];
    }

    protected function getDiskUsageData(): array
    {
        return [
            'data' => [
                'partition' => [
                    [
                        'used' => 100000000,
                        'available' => 200000000,
                        'total' => 300000000,
                        'percentage' => 33,
                        'inodes_used' => 395097,
                        'inodes_available' => 20575847,
                        'inodes_total' => 20970944,
                        'inodes_ipercentage' => 2,
                        'mount' => '/',
                    ],
                ],
            ],
        ];
    }

    protected function getPhpInstalledVersionsData(): array
    {
        return [
            'data' => [
                'versions' => [
                    'ea-php74',
                    'ea-php80',
                ],
            ],
        ];
    }

    protected function getPhpSystemVersionData(): array
    {
        return [
            'data' => [
                'version' => 'ea-php80',
            ],
        ];
    }

    protected function getWhmVersionData(): array
    {
        return [
            'data' => [
                'version' => '11.100.0.9999',
            ],
        ];
    }
}
