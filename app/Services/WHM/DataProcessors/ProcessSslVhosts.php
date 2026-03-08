<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Account;
use App\Models\AccountSslCertificate;
use App\Models\Server;

class ProcessSslVhosts
{
    public function execute(Server $server, array $data): void
    {
        $vhosts = $data['data']['vhosts'] ?? [];

        if (empty($vhosts)) {
            return;
        }

        $accounts = $server->accounts()->get()->keyBy('user');

        $processedByAccount = [];

        foreach ($vhosts as $vhost) {
            $username = $vhost['user'] ?? null;
            $servername = $vhost['servername'] ?? null;

            if (! $username || ! $servername) {
                continue;
            }

            /** @var Account|null $account */
            $account = $accounts->get($username);

            if (! $account) {
                continue;
            }

            $processedByAccount[$account->id][] = $servername;

            AccountSslCertificate::updateOrCreate(
                ['account_id' => $account->id, 'servername' => $servername],
                [
                    'user' => $username,
                    'type' => $vhost['type'] ?? 'main',
                    'vhost_domains' => $vhost['domains'] ?? [],
                    'certificate_domains' => $vhost['crt']['domains'] ?? [],
                    'expires_at' => isset($vhost['crt']['not_after'])
                        ? date('Y-m-d H:i:s', $vhost['crt']['not_after'])
                        : null,
                    'issuer' => $vhost['crt']['issuer.organizationName'] ?? null,
                ]
            );
        }

        foreach ($processedByAccount as $accountId => $servernames) {
            AccountSslCertificate::where('account_id', $accountId)
                ->whereNotIn('servername', $servernames)
                ->delete();
        }
    }
}
