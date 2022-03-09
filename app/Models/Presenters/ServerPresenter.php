<?php

namespace App\Models\Presenters;

use App\Enums\ServerTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;

trait ServerPresenter
{
    protected function whmBaseApiUrl(): Attribute
    {
        $protocol = config('server-tracker.whm.protocol');

        return Attribute::make(
            get: fn () => "$protocol://$this->address:$this->port/json-api/",
        );
    }

    protected function whmUrl(): Attribute
    {
        $protocol = ($this->port === 2087) ? 'https' : 'http';

        return Attribute::make(
            get: fn () => "$protocol://$this->address:$this->port",
        );
    }

    protected function formattedServerType(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->server_type) {
                ServerTypeEnum::Dedicated => 'Dedicated',
                ServerTypeEnum::Reseller => 'Reseller',
                ServerTypeEnum::Vps => 'VPS',
            },
        );
    }

    protected function formattedDiskUsed(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('disk_used')) {
                    return 'Unknown';
                }

                return $this->formatFileSize($this->settings->get('disk_used'));
            },
        );
    }

    protected function formattedDiskAvailable(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('disk_available')) {
                    return 'Unknown';
                }

                return $this->formatFileSize($this->settings->get('disk_available'));
            },
        );
    }

    protected function formattedDiskTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('disk_total')) {
                    return 'Unknown';
                }

                return $this->formatFileSize($this->settings->get('disk_total'));
            },
        );
    }

    protected function formattedBackupDailyDays(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('backup_daily_days')) {
                    return 'None';
                }

                return str_replace(
                    [0, 1, 2, 3, 4, 5, 6],
                    ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    $this->settings->get('backup_daily_days')
                );
            },
        );
    }

    protected function formattedBackupWeeklyDay(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('backup_weekly_day')) {
                    return 'None';
                }

                $days = [
                    'Sunday',
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                ];

                return $days[$this->settings->get('backup_weekly_day')];
            },
        );
    }

    protected function formattedBackupMonthlyDays(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('backup_monthly_days')) {
                    return 'None';
                }

                return str_replace(
                    [1, 15],
                    ['1st', '15th'],
                    $this->settings->get('backup_monthly_days')
                );
            },
        );
    }

    protected function formattedPhpInstalledVersions(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('php_installed_versions')) {
                    return 'Unknown';
                }

                return '';
            },
        );
    }

    protected function formattedPhpSystemVersion(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('php_system_version')) {
                    return 'Unknown';
                }

                $versions = [
                    'ea-php53' => '5.3',
                    'ea-php54' => '5.4',
                    'ea-php55' => '5.5',
                    'ea-php56' => '5.6',
                    'ea-php70' => '7.0',
                    'ea-php71' => '7.1',
                    'ea-php72' => '7.2',
                    'ea-php73' => '7.3',
                    'ea-php74' => '7.4',
                    'ea-php80' => '8.0',
                    'ea-php81' => '8.1',
                ];

                return Arr::get($versions, $this->settings->get('php_system_version'), 'Unknown');
            },
        );
    }

    protected function formattedWhmVersion(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings->get('whm_version')) {
                    return 'Unknown';
                }

                return $this->settings->get('whm_version');
            },
        );
    }

    protected function missingToken(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->token === null,
        );
    }

    protected function canRefreshData(): Attribute
    {
        return Attribute::make(
            get: fn () => ! $this->missing_token,
        );
    }

    private function formatFileSize($bytes): string
    {
        if ($bytes >= 1073741824) {
            $bytes = $this->trimTrailingZeroes(number_format($bytes / 1073741824, 2)) . ' TB';
        } elseif ($bytes >= 1048576) {
            $bytes = $this->trimTrailingZeroes(number_format($bytes / 1048576, 2)) . ' GB';
        } elseif ($bytes >= 1024) {
            $bytes = $this->trimTrailingZeroes(number_format($bytes / 1024, 2)) . ' MB';
        } else {
            $bytes = $bytes . ' KB';
        }

        return $bytes;
    }

    private function trimTrailingZeroes($number): string
    {
        if (! str_contains($number, '.')) {
            $number = rtrim($number, '0');
        }

        return rtrim($number, '.');
    }
}
