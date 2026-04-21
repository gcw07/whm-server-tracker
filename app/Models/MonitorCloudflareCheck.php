<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $monitor_id
 * @property bool $enabled
 * @property string|null $cloudflare_zone_id
 * @property string|null $cloudflare_account_id
 * @property string|null $zone_status
 * @property CarbonImmutable|null $last_synced_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Collection<int, CloudflareAnalytic> $analytics
 * @property-read int|null $analytics_count
 * @property-read Monitor $monitor
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereCloudflareZoneId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereLastSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorCloudflareCheck whereZoneStatus($value)
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class MonitorCloudflareCheck extends Model
{
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(CloudflareAnalytic::class);
    }
}
