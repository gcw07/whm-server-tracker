<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Models\Monitor;

/**
 * App\Models\LighthouseAudit
 *
 * @property int $id
 * @property int $monitor_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $performance_score
 * @property int $accessibility_score
 * @property int $best_practices_score
 * @property int $seo_score
 * @property int $pwa_score
 * @property int $speed_index
 * @property mixed|null $raw_results
 * @property string|null $report
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Database\Factories\LighthouseAuditFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereAccessibilityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereBestPracticesScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit wherePerformanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit wherePwaScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereRawResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereReport($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereSeoScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereSpeedIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LighthouseAudit whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class LighthouseAudit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
