<?php

namespace App\Models;

use App\Casts\Settings;
use App\Enums\ServerTypeEnum;
use App\Filters\ServerFilters;
use App\Jobs\FetchServerDataJob;
use App\Models\Presenters\ServerPresenter;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\UptimeMonitor\Models\Monitor;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property int $port
 * @property ServerTypeEnum $server_type
 * @property mixed|null $token
 * @property string|null $notes
 * @property \App\Collections\SettingsCollection|null $settings
 * @property \Illuminate\Support\Carbon|null $server_update_last_failed_at
 * @property \Illuminate\Support\Carbon|null $server_update_last_succeeded_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Account> $accounts
 * @property-read int|null $accounts_count
 * @property-read mixed $backups_enabled
 * @property-read mixed $can_refresh_data
 * @property-read mixed $formatted_backup_daily_days
 * @property-read mixed $formatted_backup_monthly_days
 * @property-read mixed $formatted_backup_weekly_day
 * @property-read mixed $formatted_disk_available
 * @property-read mixed $formatted_disk_total
 * @property-read mixed $formatted_disk_used
 * @property-read mixed $formatted_php_installed_versions
 * @property-read mixed $formatted_php_system_version
 * @property-read mixed $formatted_server_type
 * @property-read mixed $formatted_whm_version
 * @property-read mixed $is_disk_critical
 * @property-read mixed $is_disk_full
 * @property-read mixed $is_disk_warning
 * @property-read mixed $missing_token
 * @property-read mixed $whm_base_api_url
 * @property-read mixed $whm_url
 *
 * @method static \Database\Factories\ServerFactory factory($count = null, $state = [])
 * @method static Builder<static>|Server newModelQuery()
 * @method static Builder<static>|Server newQuery()
 * @method static Builder<static>|Server query()
 * @method static Builder<static>|Server whereAddress($value)
 * @method static Builder<static>|Server whereCreatedAt($value)
 * @method static Builder<static>|Server whereId($value)
 * @method static Builder<static>|Server whereName($value)
 * @method static Builder<static>|Server whereNotes($value)
 * @method static Builder<static>|Server wherePort($value)
 * @method static Builder<static>|Server whereServerType($value)
 * @method static Builder<static>|Server whereServerUpdateLastFailedAt($value)
 * @method static Builder<static>|Server whereServerUpdateLastSucceededAt($value)
 * @method static Builder<static>|Server whereSettings($value)
 * @method static Builder<static>|Server whereToken($value)
 * @method static Builder<static>|Server whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Server extends Model
{
    use HasFactory, ServerPresenter;

    protected $guarded = [];

    protected $appends = [
        'whm_url',
        'formatted_server_type',
        'formatted_disk_used',
        'formatted_disk_available',
        'formatted_disk_total',
        'formatted_backup_daily_days',
        'formatted_backup_weekly_day',
        'formatted_backup_monthly_days',
        'formatted_php_installed_versions',
        'formatted_php_system_version',
        'formatted_whm_version',
        'backups_enabled',
        'is_disk_warning',
        'is_disk_critical',
        'is_disk_full',
        'missing_token',
        'can_refresh_data',
    ];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'server_type' => ServerTypeEnum::class,
            'token' => 'encrypted',
            'settings' => Settings::class,
            'server_update_last_failed_at' => 'datetime',
            'server_update_last_succeeded_at' => 'datetime',
        ];
    }

    public static function refreshData(): void
    {
        $servers = static::query()->withTokens()->get();

        $servers->each(fn ($server) => dispatch(new FetchServerDataJob($server)));
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function addAccount($account): Account
    {
        return $this->accounts()->create($account);
    }

    public function removeAccount($account)
    {
        return $account->delete();
    }

    public function findAccount($username)
    {
        return $this->fresh()->accounts()->where('user', $username)->first();
    }

    public function removeMonitors()
    {
        foreach ($this->accounts as $account) {
            if (Account::where('domain', $account->domain)->count() > 1) {
                continue;
            }

            if ($monitor = Monitor::where('url', $account->domain_url)->first()) {
                $monitor->delete();
            }
        }
    }

    #[Scope]
    public function withTokens(Builder $query): void
    {
        $query->whereNotNull('token');
    }

    #[Scope]
    public function filter($query, ServerFilters $filters)
    {
        return $filters->apply($query);
    }

    #[Scope]
    public function search(Builder $query, string $term): void
    {
        $query->whereAny([
            'name',
            'notes',
        ], 'LIKE', "%$term%");
    }
}
