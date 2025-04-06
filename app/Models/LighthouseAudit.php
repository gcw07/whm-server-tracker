<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\UptimeMonitor\Models\Monitor;

/**
 * @property int $id
 * @property int $monitor_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $performance_score
 * @property int $accessibility_score
 * @property int $best_practices_score
 * @property int $seo_score
 * @property int $pwa_score
 * @property int $speed_index
 * @property string|null $raw_results
 * @property string|null $report
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Database\Factories\LighthouseAuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereAccessibilityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereBestPracticesScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit wherePerformanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit wherePwaScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereRawResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereSeoScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereSpeedIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LighthouseAudit extends Model
{
    use HasFactory;

    protected $guarded = [];

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
