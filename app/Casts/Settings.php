<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Support\Collection;

class Settings implements CastsAttributes
{
    protected array $allowed = [
        'disk_used', 'disk_available', 'disk_total', 'disk_percentage',
        'backup_enabled', 'backup_days', 'backup_retention',
    ];

    public function get($model, $key, $value, $attributes): ?Collection
    {
        return isset($attributes[$key]) ? new Collection(json_decode($attributes[$key], true)) : null;
    }

    public function set($model, $key, $value, $attributes): array
    {
        if (! $value instanceof Collection) {
            return [$key => json_encode([])];
        }

        return [$key => json_encode($value->only($this->allowed))];
    }
}
