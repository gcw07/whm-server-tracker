<?php

namespace App\Models\Presenters;

use App\Enums\ServerTypeEnum;
use App\Services\PhpVersions;
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
                if ($this->settings?->has('disk_used')) {
                    return $this->formatFileSize($this->settings->get('disk_used'));
                }

                return 'Unknown';
            },
        );
    }

    protected function formattedDiskAvailable(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('disk_available')) {
                    return $this->formatFileSize($this->settings->get('disk_available'));
                }

                return 'Unknown';
            },
        );
    }

    protected function formattedDiskTotal(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('disk_total')) {
                    return $this->formatFileSize($this->settings->get('disk_total'));
                }

                return 'Unknown';
            },
        );
    }

    protected function formattedBackupDailyDays(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('backup_daily_days')) {
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
                }

                return 'None';
            },
        );
    }

    protected function formattedBackupWeeklyDay(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('backup_weekly_day')) {
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
                }

                return 'None';
            },
        );
    }

    protected function formattedBackupMonthlyDays(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('backup_monthly_days')) {
                    $values = explode(',', $this->settings->get('backup_monthly_days'));

                    return collect($values)->map(fn ($item) => match ($item) {
                        '1' => '1st',
                        '15' => '15th',
                    })->join(', ');
                }

                return 'None';
            },
        );
    }

    protected function formattedPhpInstalledVersions(): Attribute
    {
        return Attribute::make(
            get: function () {
                $phpVersions = PhpVersions::filtered('version');

                if ($this->settings?->has('php_installed_versions')) {
                    return collect($this->settings->get('php_installed_versions'))
                        ->reject(fn ($item) => $item == 'nf-php74')
                        ->map(function ($version) use ($phpVersions) {
                            return $phpVersions->get(substr($version, 3), 'Unknown');
                        })
                        ->toArray();
                }

                return ['Unknown'];
            },
        );
    }

    protected function formattedPhpSystemVersion(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('php_system_version')) {
                    $phpVersions = PhpVersions::filtered('version');

                    return Arr::get($phpVersions, substr($this->settings->get('php_system_version'), 3), 'Unknown');
                }

                return 'Unknown';
            },
        );
    }

    protected function formattedWhmVersion(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('whm_version')) {
                    [, $version] = explode('.', $this->settings->get('whm_version'), 2);

                    return "v$version";
                }

                return 'Unknown';
            },
        );
    }

    protected function backupsEnabled(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->settings?->has('backup_enabled')) {
                    return $this->settings->get('backup_enabled');
                }

                return false;
            },
        );
    }

    protected function isDiskWarning(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskWarning = config('server-tracker.disk_usage.server_disk_warning');
                $diskCritical = config('server-tracker.disk_usage.server_disk_critical');

                $percentage = $this->settings?->has('disk_percentage') ? $this->settings->get('disk_percentage') : null;

                return $percentage ? $percentage >= $diskWarning && $percentage < $diskCritical : null;
            },
        );
    }

    protected function isDiskCritical(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskCritical = config('server-tracker.disk_usage.server_disk_critical');
                $diskFull = config('server-tracker.disk_usage.server_disk_full');

                $percentage = $this->settings?->has('disk_percentage') ? $this->settings->get('disk_percentage') : null;

                return $percentage ? $percentage >= $diskCritical && $percentage < $diskFull : null;
            },
        );
    }

    protected function isDiskFull(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskFull = config('server-tracker.disk_usage.server_disk_full');

                $percentage = $this->settings?->has('disk_percentage') ? $this->settings->get('disk_percentage') : null;

                return $percentage ? $percentage >= $diskFull : null;
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

    public function isPhpVersionActive($version): bool
    {
        $phpVersions = PhpVersions::active('version');

        return $phpVersions->contains($version);
    }

    public function isPhpVersionSecurityOnly($version): bool
    {
        $phpVersions = PhpVersions::security('version');

        return $phpVersions->contains($version);
    }

    public function isPhpVersionEndOfLife($version): bool
    {
        $phpVersions = PhpVersions::endOfLife('version');

        return $phpVersions->contains($version);
    }

    protected function formatFileSize($kilobytes, $precision = null): string
    {
        $byteUnits = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $bytePrecision = [0, 1, 2, 2, 3, 3, 4, 4];
        $byteNext = 1024;

        $kilobytes = (int) $kilobytes;
        for ($i = 0; ($kilobytes / $byteNext) >= 0.9 && $i < count($byteUnits); $i++) {
            $kilobytes /= $byteNext;
        }

        return round($kilobytes, is_null($precision) ? $bytePrecision[$i] : (int) $precision).' '.$byteUnits[$i];
    }
}
