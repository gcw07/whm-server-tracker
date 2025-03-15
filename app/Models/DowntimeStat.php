<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Models\Monitor;

/**
 * App\Models\DowntimeStat
 *
 * @property int $id
 * @property int $monitor_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $downtime_period
 * @property-read Monitor $monitor
 *
 * @method static \Database\Factories\DowntimeStatFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|DowntimeStat newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DowntimeStat newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DowntimeStat query()
 * @method static \Illuminate\Database\Eloquent\Builder|DowntimeStat whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DowntimeStat whereDowntimePeriod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DowntimeStat whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DowntimeStat whereMonitorId($value)
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
