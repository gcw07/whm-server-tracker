<?php

namespace Tests\Factories;

use App\Services\WHM\DataProcessors\ProcessAccountEmails;
use App\Services\WHM\WhmEmailDiskUsage;

class WhmEmailDiskUsageFake extends WhmEmailDiskUsage
{
    public function fetch(): void
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
