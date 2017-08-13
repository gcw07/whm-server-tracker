<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $guarded = [];
    protected $casts = ['backup_enabled' => 'boolean'];
    protected $dates = ['details_last_updated', 'accounts_last_updated'];
    protected $appends = ['formatted_server_type', 'missing_token'];
    protected $hidden = ['token'];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function addAccount($account)
    {
        return $this->accounts()->create($account);
    }

    public function removeAccount($account)
    {
        return $account->delete();
    }

    public function findAccount($username)
    {
        return $this->fresh()->accounts()->where('user', $username)->first();
    }

    public function fetchDiskUsageDetails($serverConnector)
    {
        try {
            $diskUsage = $serverConnector->getDiskUsage();

            $this->update([
                'disk_used' => $diskUsage['used'],
                'disk_available' => $diskUsage['available'],
                'disk_total' => $diskUsage['total'],
                'disk_percentage' => $diskUsage['percentage'],
                'disk_last_updated' => Carbon::now()
            ]);
        } catch (InvalidServerTypeException $e) {

        } catch (MissingTokenException $e) {

        } catch (ServerConnectionException $e) {

        } catch (ForbiddenAccessException $e) {

        }

        return false;
    }

    public function fetchBackupDetails($serverConnector)
    {
        try {
            $backups = $serverConnector->getBackups();

            $this->update([
                'backup_enabled' => $backups['backupenable'],
                'backup_days' => $backups['backupdays'],
                'backup_retention' => $backups['backup_daily_retention'],
                'backup_last_updated' => Carbon::now()
            ]);
        } catch (InvalidServerTypeException $e) {

        } catch (MissingTokenException $e) {

        } catch (ServerConnectionException $e) {

        } catch (ForbiddenAccessException $e) {

        }

        return false;
    }

    public function fetchAccounts($serverConnector)
    {
        try {
            $accounts = $serverConnector->getAccounts();

            $this->processAccounts($accounts);

            $this->update([
                'accounts_last_updated' => Carbon::now()
            ]);
        } catch (InvalidServerTypeException $e) {

        } catch (MissingTokenException $e) {

        } catch (ServerConnectionException $e) {

        } catch (ForbiddenAccessException $e) {

        }

        return false;
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
        if ($foundAccount = $this->findAccount($account['user'])) {
            return $foundAccount->update($account);
        }

        return $this->addAccount($account);
    }

    public function removeStaleAccounts($accounts)
    {
        $this->fresh()->accounts->filter(function ($item) use ($accounts) {
            if (collect($accounts)->where('user', $item['user'])->first()) {
                return false;
            }

            return true;
        })->each(function ($item) {
            $this->removeAccount($item);
        });
    }

    public function getFormattedServerTypeAttribute()
    {
        if ($this->server_type == 'vps') {
            return 'VPS';
        } elseif ($this->server_type == 'dedicated') {
            return 'Dedicated';
        }

        return 'Reseller';
    }

    public function getMissingTokenAttribute()
    {
        if ($this->server_type != 'reseller' && $this->token === null) {
            return true;
        }

        return false;
    }
}
