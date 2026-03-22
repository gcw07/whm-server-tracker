<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property CarbonImmutable $started_at
 * @property CarbonImmutable $ended_at
 * @property int $duration_seconds
 * @property CarbonImmutable|null $created_at
 * @property-read Monitor $monitor
 *
 * @method static \Database\Factories\MonitorOutageFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorOutage whereStartedAt($value)
 *
 * @mixin \Eloquent
 */
#[Table(timestamps: false)]
#[Unguarded]
class MonitorOutage extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
