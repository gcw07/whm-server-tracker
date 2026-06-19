<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property bool $has_domain_property
 * @property string|null $domain_property
 * @property string|null $dns_txt_record
 * @property bool $dns_added_to_cloudflare
 * @property CarbonImmutable|null $last_synced_at
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Monitor $monitor
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorSearchConsoleCheck newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorSearchConsoleCheck newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|MonitorSearchConsoleCheck query()
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class MonitorSearchConsoleCheck extends Model
{
    protected function casts(): array
    {
        return [
            'has_domain_property' => 'boolean',
            'dns_added_to_cloudflare' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
