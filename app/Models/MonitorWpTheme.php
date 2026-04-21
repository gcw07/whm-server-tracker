<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property string $name
 * @property string $slug
 * @property string $version
 * @property bool $active
 * @property bool $update_available
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Monitor $monitor
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class MonitorWpTheme extends Model
{
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'update_available' => 'boolean',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
