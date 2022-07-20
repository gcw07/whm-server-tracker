<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Server;
use Carbon\Carbon;

class ProcessAccounts
{
    public function execute(Server $server, $data)
    {
        $accounts = $data['data']['acct'];
        $config = config('server-tracker');

        collect($accounts)
            ->map(fn ($item) => [
                'domain' => $item['domain'],
                'user' => $item['user'],
                'ip' => $item['ip'],
                'backup' => $item['backup'],
                'suspended' => $item['suspended'],
                'suspend_reason' => $item['suspendreason'],
                'suspend_time' => ($item['suspendtime'] != 0 ? Carbon::createFromTimestamp($item['suspendtime']) : null),
                'setup_date' => Carbon::parse($item['startdate']),
                'disk_used' => $item['diskused'],
                'disk_limit' => $item['disklimit'],
                'plan' => $item['plan'],
            ])
            ->reject(fn ($item) => in_array($item['user'], $config['ignore_usernames']))
            ->each(fn ($item) => $this->addOrUpdateAccount($server, $item));

        $this->removeStaleAccounts($server, $accounts);
    }

    public function addOrUpdateAccount(Server $server, $account)
    {
        if ($foundAccount = $server->findAccount($account['user'])) {
            return $foundAccount->update($account);
        }

        return $server->addAccount($account);
    }

    public function removeStaleAccounts(Server $server, $accounts)
    {
        $server->fresh()->accounts->filter(function ($item) use ($accounts) {
            if (collect($accounts)->firstWhere('user', $item['user'])) {
                return false;
            }

            return true;
        })->each(fn ($item) => $server->removeAccount($item));
    }
}
