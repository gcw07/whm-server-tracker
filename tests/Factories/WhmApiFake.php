<?php

namespace Tests\Factories;

use App\Exceptions\Server\MissingTokenException;
use App\Models\Server;
use App\Services\WHM\DataProcessors\ProcessAccountEmails;
use App\Services\WHM\DataProcessors\ProcessAccounts;
use App\Services\WHM\DataProcessors\ProcessBackups;
use App\Services\WHM\DataProcessors\ProcessDiskUsage;
use App\Services\WHM\DataProcessors\ProcessPhpInstalledVersions;
use App\Services\WHM\DataProcessors\ProcessPhpSystemVersion;
use App\Services\WHM\DataProcessors\ProcessPhpVhostVersions;
use App\Services\WHM\DataProcessors\ProcessSslVhosts;
use App\Services\WHM\DataProcessors\ProcessWhmVersion;
use App\Services\WHM\WhmApi;

class WhmApiFake extends WhmApi
{
    protected Server $server;

    public function setServer(Server $server): void
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

    public function fetchEmailDiskUsage(): void
    {
        $accounts = $this->server->accounts()->get();

        foreach ($accounts as $account) {
            $data = $this->getEmailDiskUsageData($account->user);
            $systemBytes = $this->getSystemEmailDiskUsageBytes($account->user);

            $data['result']['data'][] = [
                'email' => $account->user,
                'user' => 'system',
                'domain' => $account->domain,
                '_diskused' => $systemBytes,
                '_diskquota' => 0,
                'diskusedpercent_float' => 0,
                'suspended_incoming' => 0,
                'suspended_login' => 0,
            ];

            (new ProcessAccountEmails)->execute($account, $data);
        }
    }

    protected function getSystemEmailDiskUsageBytes(string $username): int
    {
        return 2048000;
    }

    public function enrichServerData(): void
    {
        (new ProcessSslVhosts)->execute($this->server, $this->getSslVhostsData());
        (new ProcessPhpVhostVersions)->execute($this->server, $this->getPhpVhostVersionsData());
    }

    protected function getPhpVhostVersionsData(): array
    {
        return [
            'data' => [
                'versions' => [
                    ['vhost' => 'my-site.com', 'version' => 'ea-php81'],
                    ['vhost' => 'super-system.com', 'version' => 'ea-php82'],
                ],
            ],
        ];
    }

    protected function getSslVhostsData(): array
    {
        return [
            'data' => [
                'vhosts' => [
                    [
                        'user' => 'mysite',
                        'servername' => 'my-site.com',
                        'type' => 'main',
                        'domains' => ['my-site.com', 'www.my-site.com'],
                        'crt' => [
                            'not_after' => 1893456000,
                            'domains' => ['my-site.com', 'www.my-site.com'],
                            'issuer.organizationName' => "Let's Encrypt",
                        ],
                    ],
                    [
                        'user' => 'mysite',
                        'servername' => 'sub.my-site.com',
                        'type' => 'sub',
                        'domains' => ['sub.my-site.com'],
                        'crt' => [
                            'not_after' => 1893456000,
                            'domains' => ['sub.my-site.com'],
                            'issuer.organizationName' => "Let's Encrypt",
                        ],
                    ],
                    [
                        'user' => 'super',
                        'servername' => 'super-system.com',
                        'type' => 'main',
                        'domains' => ['super-system.com', 'www.super-system.com'],
                        'crt' => [
                            'not_after' => 1893456000,
                            'domains' => ['super-system.com'],
                            'issuer.organizationName' => "Let's Encrypt",
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getEmailDiskUsageData(string $username): array
    {
        return [
            'result' => [
                'data' => [
                    [
                        'email' => "info@{$username}.com",
                        'user' => 'info',
                        'domain' => "{$username}.com",
                        '_diskused' => 1024000,
                        '_diskquota' => 524288000,
                        'diskusedpercent_float' => 0.19,
                        'suspended_incoming' => 0,
                        'suspended_login' => 0,
                    ],
                    [
                        'email' => "admin@{$username}.com",
                        'user' => 'admin',
                        'domain' => "{$username}.com",
                        '_diskused' => 5120000,
                        '_diskquota' => 0,
                        'diskusedpercent_float' => 0,
                        'suspended_incoming' => 0,
                        'suspended_login' => 0,
                    ],
                ],
            ],
        ];
    }
}
