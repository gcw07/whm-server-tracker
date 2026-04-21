<?php

namespace App\Models;

use App\Enums\WordPressStatusEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $monitor_id
 * @property bool $enabled
 * @property WordPressStatusEnum $status
 * @property string|null $wordpress_version
 * @property string|null $failure_reason
 * @property string|null $php_version
 * @property string|null $site_name
 * @property string|null $active_theme
 * @property string|null $active_theme_version
 * @property int|null $plugins_installed_count
 * @property int|null $themes_installed_count
 * @property int|null $plugin_updates_count
 * @property int|null $theme_updates_count
 * @property string|null $check_source
 * @property string|null $agent_version
 * @property CarbonImmutable|null $last_response_at
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
#[Table('monitor_wordpress_checks')]
#[Unguarded]
class MonitorWordPressCheck extends Model
{
    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => WordPressStatusEnum::class,
            'last_response_at' => 'immutable_datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
