<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\UptimeMonitor\Models\Monitor;

/**
 * @property int $id
 * @property int $monitor_id
 * @property \Carbon\CarbonImmutable $date
 * @property int $downtime_period
 * @property-read Monitor $monitor
 *
 * @method static \Database\Factories\DowntimeStatFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DowntimeStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DowntimeStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DowntimeStat query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DowntimeStat whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DowntimeStat whereDowntimePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DowntimeStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DowntimeStat whereMonitorId($value)
 *
 * @mixin \Eloquent
 */
class DowntimeStat extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
