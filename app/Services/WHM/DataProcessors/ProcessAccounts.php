<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Server;
use Carbon\Carbon;
use Spatie\UptimeMonitor\Models\Monitor;

class ProcessAccounts
{
    public function execute(Server $server, $data)
    {
        if (! array_key_exists('data', $data)) {
            return [];
        }

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

    protected function addOrUpdateAccount(Server $server, $account)
    {
        if ($foundAccount = $server->findAccount($account['user'])) {
            $this->updateMonitor($foundAccount, $account);

            return $foundAccount->update($account);
        }

        $this->addMonitor($account);

        return $server->addAccount($account);
    }

    protected function removeStaleAccounts(Server $server, $accounts)
    {
        $server->fresh()->accounts->filter(function ($item) use ($accounts) {
            if (collect($accounts)->firstWhere('user', $item['user'])) {
                return false;
            }

            return true;
        })->each(function ($item) use ($server) {
            $this->removeMonitor($item);

            return $server->removeAccount($item);
        });
    }

    protected function addMonitor($account)
    {
        if ($account['suspended']) {
            return;
        }

        $url = trim('https://'.$account['domain'], '/');

        Monitor::create([
            'url' => $url,
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => true,
        ]);
    }

    protected function updateMonitor($account, $attributes)
    {
        // If account suspended status has changed
        if ($account->suspended != $attributes['suspended']) {
            $this->removeMonitor($account);
            $this->addMonitor($attributes);

            return;
        }

        // If account domain name has not changed, do nothing
        if ($account->domain === $attributes['domain']) {
            return;
        }

        $this->removeMonitor($account);
        $this->addMonitor($attributes);
    }

    protected function removeMonitor($account)
    {
        if ($monitor = Monitor::where('url', $account->domain_url)->first()) {
            $monitor->delete();
        }
    }
}
