<?php

namespace App\Models;

use App\Enums\LighthouseStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property string $form_factor
 * @property bool $enabled
 * @property LighthouseStatusEnum $status
 * @property CarbonImmutable|null $last_failed_at
 * @property CarbonImmutable|null $last_succeeded_at
 * @property string|null $failure_reason
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereFailureReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereLastFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereLastSucceededAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereMonitorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorLighthouseCheck whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class MonitorLighthouseCheck extends Model
{
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => LighthouseStatusEnum::class,
            'last_failed_at' => 'datetime',
            'last_succeeded_at' => 'datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
