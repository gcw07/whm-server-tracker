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
                if (! $this->settings?->has('disk_used')) {
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
                if (! $this->settings?->has('disk_available')) {
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
                if (! $this->settings?->has('disk_total')) {
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
                if (! $this->settings?->has('backup_daily_days')) {
                    return 'None';
                }

                $values = explode(',', $this->settings->get('backup_daily_days'));

                return collect($values)->map(fn ($item) => match ($item) {
                    '0' => 'Sun',
                    '1' => 'Mon',
                    '2' => 'Tue',
                    '3' => 'Wed',
                    '4' => 'Thu',
                    '5' => 'Fri',
                    '6' => 'Sat',
                })->join(', ');
            },
        );
    }

    protected function formattedBackupWeeklyDay(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings?->has('backup_weekly_day')) {
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
                if (! $this->settings?->has('backup_monthly_days')) {
                    return 'None';
                }

                $values = explode(',', $this->settings->get('backup_monthly_days'));

                return collect($values)->map(fn ($item) => match ($item) {
                    '1' => '1st',
                    '15' => '15th',
                })->join(', ');
            },
        );
    }

    protected function formattedPhpInstalledVersions(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings?->has('php_installed_versions')) {
                    return ['Unknown'];
                }

                return collect($this->settings->get('php_installed_versions'))
                    ->map(fn ($item) => match ($item) {
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
                        default => 'Unknown'
                    })->toArray();
            },
        );
    }

    protected function formattedPhpSystemVersion(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->settings?->has('php_system_version')) {
                    return 'Unknown';
                }

                $versions = [
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
                if (! $this->settings?->has('whm_version')) {
                    return 'Unknown';
                }

                [, $version] = explode('.', $this->settings->get('whm_version'), 2);

                return "v$version";
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

    private function formatFileSize($kilobytes, $precision = null): string
    {
        $byteUnits = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $bytePrecision = [0, 1, 2, 2, 3, 3, 4, 4];
        $byteNext = 1024;

        $kilobytes = (int) $kilobytes;
        for ($i = 0; ($kilobytes / $byteNext) >= 0.9 && $i < count($byteUnits); $i++) {
            $kilobytes /= $byteNext;
        }

        return round($kilobytes, is_null($precision) ? $bytePrecision[$i] : (int) $precision) . ' ' . $byteUnits[$i];
    }
}
