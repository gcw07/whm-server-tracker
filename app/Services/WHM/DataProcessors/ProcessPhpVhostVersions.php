<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Account;
use App\Models\Server;

class ProcessPhpVhostVersions
{
    public function execute(Server $server, array $data): void
    {
        $versions = $data['data']['versions'] ?? [];

        if (empty($versions)) {
            return;
        }

        $accounts = $server->accounts()->get()->keyBy('domain');

        foreach ($versions as $version) {
            $domain = $version['vhost'] ?? null;
            $phpVersion = $version['version'] ?? null;

            if (! $domain || ! $phpVersion) {
                continue;
            }

            /** @var Account|null $account */
            $account = $accounts->get($domain);

            if (! $account) {
                continue;
            }

            $account->update(['php_version' => $phpVersion]);
        }
    }
}
