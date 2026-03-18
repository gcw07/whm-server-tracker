<?php

namespace App\Models;

use App\Enums\BlacklistStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property bool $enabled
 * @property BlacklistStatusEnum $status
 * @property string|null $failure_reason
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorBlacklistCheck whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class MonitorBlacklistCheck extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => BlacklistStatusEnum::class,
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
