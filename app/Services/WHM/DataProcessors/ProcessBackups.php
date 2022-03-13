<?php

namespace App\Services\WHM\DataProcessors;

use App\Models\Server;

class ProcessBackups
{
    public function execute(Server $server, $data)
    {
        $backups = $data['data']['backup_config'];

        $server->settings->merge([
            'backup_enabled' => $backups['backupenable'],
            'backup_daily_enabled' => $backups['backup_daily_enable'],
            'backup_daily_retention' => $backups['backup_daily_retention'],
            'backup_daily_days' => $backups['backupdays'],
            'backup_weekly_enabled' => $backups['backup_weekly_enable'],
            'backup_weekly_retention' => $backups['backup_weekly_retention'],
            'backup_weekly_day' => $backups['backup_weekly_day'],
            'backup_monthly_enabled' => $backups['backup_monthly_enable'],
            'backup_monthly_retention' => $backups['backup_monthly_retention'],
            'backup_monthly_days' => $backups['backup_monthly_dates'],
        ]);

        $server->save();
    }
}
