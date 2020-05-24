<?php

namespace App\Models;

use Carbon\Carbon;

class Fetchers
{
    /**
     * The Server instance
     *
     * @var Server
     */
    protected $server;

    /**
     * Create a new fetchers instance.
     *
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function diskUsage($serverConnector)
    {
        $diskUsage = $serverConnector->getDiskUsage();

        if ($diskUsage === false) {
            return false;
        }

        $this->server->settings()->merge([
            'disk_used'       => $diskUsage['used'],
            'disk_available'  => $diskUsage['available'],
            'disk_total'      => $diskUsage['total'],
            'disk_percentage' => $diskUsage['percentage']
        ]);

        return true;
    }

    public function backup($serverConnector)
    {
        $backups = $serverConnector->getBackups();

        if ($backups === false) {
            return false;
        }

        $this->server->settings()->merge([
            'backup_enabled'   => $backups['backupenable'],
            'backup_days'      => $backups['backupdays'],
            'backup_retention' => $backups['backup_daily_retention']
        ]);

        return true;
    }

    public function phpVersion($serverConnector)
    {
        $version = $serverConnector->getPhpVersion();

        if ($version === false) {
            return false;
        }

        $this->server->settings()->set('php_version', $version);

        return true;
    }

    public function accounts($serverConnector)
    {
        $accounts = $serverConnector->getAccounts();

        if ($accounts === false) {
            return false;
        }

        $this->processAccounts($accounts);

        $this->server->update([
            'accounts_last_updated' => Carbon::now()
        ]);

        return true;
    }

    public function processAccounts($accounts)
    {
        $config = config('server-tracker');

        collect($accounts)
            ->map(function ($item) {
                return [
                    'domain'         => $item['domain'],
                    'user'           => $item['user'],
                    'ip'             => $item['ip'],
                    'backup'         => $item['backup'],
                    'suspended'      => $item['suspended'],
                    'suspend_reason' => $item['suspendreason'],
                    'suspend_time'   => ($item['suspendtime'] != 0 ? Carbon::createFromTimestamp($item['suspendtime']) : null),
                    'setup_date'     => Carbon::parse($item['startdate']),
                    'disk_used'      => $item['diskused'],
                    'disk_limit'     => $item['disklimit'],
                    'plan'           => $item['plan']
                ];
            })->reject(function ($item) use ($config) {
                return in_array($item['user'], $config['ignore_usernames']);
            })->each(function ($item) {
                $this->addOrUpdateAccount($item);
            });

        $this->removeStaleAccounts($accounts);
    }

    public function addOrUpdateAccount($account)
    {
        if ($foundAccount = $this->server->findAccount($account['user'])) {
            return $foundAccount->update($account);
        }

        return $this->server->addAccount($account);
    }

    public function removeStaleAccounts($accounts)
    {
        $this->server->fresh()->accounts->filter(function ($item) use ($accounts) {
            if (collect($accounts)->where('user', $item['user'])->first()) {
                return false;
            }

            return true;
        })->each(function ($item) {
            $this->server->removeAccount($item);
        });
    }
}
