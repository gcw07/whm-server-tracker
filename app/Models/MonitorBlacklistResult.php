<?php

namespace App\Models;

use App\Services\Blacklist\BlacklistChecker;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property string $driver
 * @property string|null $checked_value
 * @property bool $listed
 * @property string|null $failure_reason
 * @property CarbonImmutable|null $checked_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read string|null $url
 * @property-read Monitor $monitor
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistResult newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistResult newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistResult query()
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class MonitorBlacklistResult extends Model
{
    protected function casts(): array
    {
        return [
            'listed' => 'boolean',
            'checked_at' => 'immutable_datetime',
        ];
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn () => BlacklistChecker::driverUrls()[$this->driver] ?? null);
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
