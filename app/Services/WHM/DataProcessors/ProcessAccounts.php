<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Account;
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
            $monitorId = $this->updateMonitor($foundAccount, $account);
            $account['monitor_id'] = $monitorId;

            return $foundAccount->update($account);
        }

        $monitorId = $this->addMonitor($account);
        $account['monitor_id'] = $monitorId;

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
            return null;
        }

        $url = trim('https://'.$account['domain'], '/');

        $monitor = Monitor::firstOrCreate(
            ['url' => $url],
            [
                'uptime_check_enabled' => true,
                'certificate_check_enabled' => true,
            ]
        );

        return $monitor->id;
    }

    protected function updateMonitor($account, $attributes)
    {
        // If account suspended status has changed
        if ($account->suspended != $attributes['suspended']) {
            $this->removeMonitor($account);

            return $this->addMonitor($attributes);
        }

        // If account domain name has not changed, keep existing monitor
        if ($account->domain === $attributes['domain']) {
            return $account->monitor_id;
        }

        // Domain changed - remove old monitor and create new one
        $this->removeMonitor($account);

        return $this->addMonitor($attributes);
    }

    protected function removeMonitor($account)
    {
        if (! $account->monitor_id) {
            return;
        }

        // Only delete monitor if this is the last account using it
        if (Account::where('monitor_id', $account->monitor_id)->count() > 1) {
            return;
        }

        if ($monitor = Monitor::find($account->monitor_id)) {
            $monitor->delete();
        }
    }
}
