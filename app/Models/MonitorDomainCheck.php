<?php

namespace App\Models;

use App\Enums\DomainNameStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property bool $enabled
 * @property DomainNameStatusEnum $status
 * @property CarbonImmutable|null $expiration_date
 * @property string|null $failure_reason
 * @property array<array-key, mixed>|null $nameservers
 * @property bool $is_on_cloudflare
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereExpirationDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereIsOnCloudflare($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereNameservers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorDomainCheck whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MonitorDomainCheck extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => DomainNameStatusEnum::class,
            'expiration_date' => 'datetime',
            'nameservers' => 'array',
            'is_on_cloudflare' => 'boolean',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
