<?php

namespace App\Casts;

use App\Collections\NotificationsCollection;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class Notifications implements CastsAttributes
{
    protected array $allowed = [
        'uptime_check_failed',
        'uptime_check_succeeded',
        'uptime_check_recovered',
        'certificate_check_succeeded',
        'certificate_check_failed',
        'certificate_expires_soon',
        'fetched_server_data_succeeded',
        'fetched_server_data_failed',
        'domain_name_expires_soon',
    ];

    public function get($model, $key, $value, $attributes): ?NotificationsCollection
    {
        return isset($attributes[$key]) ? new NotificationsCollection(json_decode($attributes[$key], true)) : new NotificationsCollection([]);
    }

    public function set($model, $key, $value, $attributes): array
    {
        if (! $value instanceof NotificationsCollection) {
            $value = new NotificationsCollection($value);
        }

        return [$key => json_encode($value->only($this->allowed))];
    }
}
