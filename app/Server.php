<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $guarded = [];
    protected $casts = ['backup_enabled' => 'boolean'];
    protected $dates = ['details_last_updated', 'accounts_last_updated'];

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

        return collect($accounts)
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
    }

    public function addOrUpdateAccount($account)
    {
        if ($foundAccount = $this->fresh()->accounts()->where('user', $account['user'])->first()) {
            return $foundAccount->update($account);
        }

        return $this->addAccount($account);
    }
}
