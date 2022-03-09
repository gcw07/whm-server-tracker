<?php

namespace App\Casts;

use App\Collections\SettingsCollection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Settings implements CastsAttributes
{
    protected array $allowed = [
        'disk_used',
        'disk_available',
        'disk_total',
        'disk_percentage',
        'backup_enabled',
        'backup_daily_enabled',
        'backup_daily_retention',
        'backup_daily_days',
        'backup_weekly_enabled',
        'backup_weekly_retention',
        'backup_weekly_day',
        'backup_monthly_enabled',
        'backup_monthly_retention',
        'backup_monthly_days',
        'php_installed_versions',
        'php_system_version',
        'whm_version',
    ];

    public function get($model, $key, $value, $attributes): ?SettingsCollection
    {
        return isset($attributes[$key]) ? new SettingsCollection(json_decode($attributes[$key], true)) : null;
    }

    public function set($model, $key, $value, $attributes): array
    {
        if (! $value instanceof SettingsCollection) {
            $value = new SettingsCollection($value);
        }

        return [$key => json_encode($value->only($this->allowed))];
    }
}
