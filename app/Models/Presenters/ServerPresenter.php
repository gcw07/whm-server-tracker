<?php

namespace App\Models\Presenters;

use App\Enums\ServerTypeEnum;
use Illuminate\Support\Arr;

trait ServerPresenter
{
    public function getWhmBaseApiUrlAttribute(): string
    {
        $protocol = config('server-tracker.whm.protocol');

        return "$protocol://$this->address:$this->port/json-api/";
    }

    public function getFormattedServerTypeAttribute()
    {
        return match ($this->server_type) {
            ServerTypeEnum::Dedicated => 'Dedicated',
            ServerTypeEnum::Reseller => 'Reseller',
            ServerTypeEnum::Vps => 'VPS',
        };
    }

    public function getFormattedBackupDaysAttribute()
    {
        if (! $this->settings()->backup_days) {
            return 'None';
        }

        return str_replace(
            [0, 1, 2, 3, 4, 5, 6],
            ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            $this->settings()->backup_days
        );
    }

    public function getFormattedDiskUsedAttribute()
    {
        if (! $this->settings()->disk_used) {
            return 'Unknown';
        }

        return $this->formatFileSize($this->settings()->disk_used);
    }

    public function getFormattedDiskAvailableAttribute()
    {
        if (! $this->settings()->disk_available) {
            return 'Unknown';
        }

        return $this->formatFileSize($this->settings()->disk_available);
    }

    public function getFormattedDiskTotalAttribute()
    {
        if (! $this->settings()->disk_total) {
            return 'Unknown';
        }

        return $this->formatFileSize($this->settings()->disk_total);
    }

    public function getFormattedPhpVersionAttribute()
    {
        if (! $this->settings()->php_version) {
            return 'Unknown';
        }

        $versions = [
            'ea-php54' => 'PHP 5.4',
            'ea-php55' => 'PHP 5.5',
            'ea-php56' => 'PHP 5.6',
            'ea-php70' => 'PHP 7.0',
            'ea-php71' => 'PHP 7.1',
            'ea-php72' => 'PHP 7.2',
            'ea-php73' => 'PHP 7.3',
            'ea-php74' => 'PHP 7.4',
            'ea-php80' => 'PHP 8.0',
            'ea-php81' => 'PHP 8.1',
        ];

        return Arr::get($versions, $this->settings()->php_version, 'Unknown');
    }

    public function getMissingTokenAttribute()
    {
        if ($this->token === null) {
            return true;
        }

        return false;
    }

    public function getCanRefreshDataAttribute()
    {
        if ($this->server_type == 'reseller' || $this->missing_token) {
            return false;
        }

        return true;
    }

    public function getWhmUrlAttribute()
    {
        if ($this->port == 2087) {
            return "https://{$this->address}:{$this->port}";
        }

        return "http://{$this->address}:{$this->port}";
    }

    private function formatFileSize($bytes)
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
