<?php

namespace App\Models;

use App\Enums\WordPressStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property bool $enabled
 * @property WordPressStatusEnum $status
 * @property string|null $wordpress_version
 * @property string|null $failure_reason
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorWordPressCheck whereWordpressVersion($value)
 *
 * @mixin \Eloquent
 */
class MonitorWordPressCheck extends Model
{
    protected $table = 'monitor_wordpress_checks';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => WordPressStatusEnum::class,
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
