<?php

namespace App;

use App\Connectors\WHMServerConnector;
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

    public function fetchDiskUsageDetails()
    {
        try {
            $api = WHMServerConnector::create($this);

            $diskUsage = $api->getDiskUsage();

            $this->update([
                'disk_used' => $diskUsage['used'],
                'disk_available' => $diskUsage['available'],
                'disk_total' => $diskUsage['total'],
                'disk_percentage' => $diskUsage['percentage'],
            ]);
        } catch (InvalidServerTypeException $e) {

        } catch (MissingTokenException $e) {

        } catch (ServerConnectionException $e) {

        } catch (ForbiddenAccessException $e) {

        }

        return false;
    }

    public function fetchBackupDetails()
    {
        try {
            $api = WHMServerConnector::create($this);

            $backups = $api->getBackups();

            $this->update([
                'backup_enabled' => $backups['backupenable'],
                'backup_days' => $backups['backupdays'],
                'backup_retention' => $backups['backup_daily_retention'],
            ]);
        } catch (InvalidServerTypeException $e) {

        } catch (MissingTokenException $e) {

        } catch (ServerConnectionException $e) {

        } catch (ForbiddenAccessException $e) {

        }

        return false;
    }
}
