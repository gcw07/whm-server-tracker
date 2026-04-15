<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_cloudflare_check_id
 * @property CarbonImmutable $date
 * @property int|null $unique_visitors
 * @property int|null $requests_total
 * @property int|null $bandwidth_total
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read MonitorCloudflareCheck $cloudflareCheck
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereBandwidthTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereMonitorCloudflareCheckId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereRequestsTotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereUniqueVisitors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareAnalytic whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class CloudflareAnalytic extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function cloudflareCheck(): BelongsTo
    {
        return $this->belongsTo(MonitorCloudflareCheck::class, 'monitor_cloudflare_check_id');
    }
}
