<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\UptimeMonitor\Models\Monitor;

/**
 * @property int $id
 * @property int $monitor_id
 * @property int $performance_score
 * @property int $accessibility_score
 * @property int $best_practices_score
 * @property int $seo_score
 * @property int $speed_index
 * @property int|null $first_contentful_paint
 * @property int|null $largest_contentful_paint
 * @property int|null $time_to_interactive
 * @property int|null $total_blocking_time
 * @property float|null $cumulative_layout_shift
 * @property string|null $form_factor
 * @property string|null $raw_results
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Database\Factories\LighthouseAuditFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereAccessibilityScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereBestPracticesScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit wherePerformanceScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereRawResults($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereSeoScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereSpeedIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LighthouseAudit whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class LighthouseAudit extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'cumulative_layout_shift' => 'float',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
