<?php

namespace App\Models\Presenters;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait AccountPresenter
{
    protected function domainUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => "https://$this->domain",
        );
    }

    protected function cpanelUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => "https://$this->domain/cpanel",
        );
    }

    protected function formattedDiskUsage(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->getDiskPercentage() ? $this->getDiskPercentage() . '%' : 'Unknown';
            },
        );
    }

    protected function backupsEnabled(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->backup;
            },
        );
    }

    protected function isDiskWarning(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskWarning = config('server-tracker.disk_usage.account_disk_warning');
                $diskCritical = config('server-tracker.disk_usage.account_disk_critical');

                return $this->getDiskPercentage() ? $this->getDiskPercentage() >= $diskWarning && $this->getDiskPercentage() < $diskCritical : null;
            },
        );
    }

    protected function isDiskCritical(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskCritical = config('server-tracker.disk_usage.account_disk_critical');
                $diskFull = config('server-tracker.disk_usage.account_disk_full');

                return $this->getDiskPercentage() ? $this->getDiskPercentage() >= $diskCritical && $this->getDiskPercentage() < $diskFull : null;
            },
        );
    }

    protected function isDiskFull(): Attribute
    {
        return Attribute::make(
            get: function () {
                $diskFull = config('server-tracker.disk_usage.account_disk_full');

                return $this->getDiskPercentage() ? $this->getDiskPercentage() >= $diskFull : null;
            },
        );
    }

    protected function getDiskPercentage(): ?float
    {
        $diskUsed = substr($this->disk_used, 0, -1);
        $diskLimit = substr($this->disk_limit, 0, -1);

        if (is_numeric($diskLimit)) {
            return round(($diskUsed / $diskLimit) * 100, 1);
        }

        return null;
    }
}
