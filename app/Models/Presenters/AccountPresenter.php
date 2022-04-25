<?php

namespace App\Models\Presenters;

use App\Enums\ServerTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Arr;

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
                $diskUsed = substr($this->disk_used, 0, -1);
                $diskLimit = substr($this->disk_limit, 0, -1);

                if (is_numeric($diskLimit)) {
                    $percentage = round(($diskUsed / $diskLimit) * 100, 1);

                    return "$percentage%";
                }

                return 'Unknown';
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
}
