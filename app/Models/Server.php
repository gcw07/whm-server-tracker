<?php

namespace App\Models;

use App\Enums\ServerTypeEnum;
use App\Filters\ServerFilters;
use App\Jobs\FetchServerAccounts;
use App\Jobs\FetchServerDetails;
use App\Models\Presenters\ServerPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Server
 *
 * @property int $id
 * @property string $name
 * @property string $address
 * @property int $port
 * @property ServerTypeEnum $server_type
 * @property string|null $token
 * @property string|null $notes
 * @property array $settings
 * @property \Illuminate\Support\Carbon|null $server_updated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Account[] $accounts
 * @property-read int|null $accounts_count
 * @property-read mixed $can_refresh_data
 * @property-read mixed $formatted_backup_days
 * @property-read mixed $formatted_disk_available
 * @property-read mixed $formatted_disk_total
 * @property-read mixed $formatted_disk_used
 * @property-read mixed $formatted_php_version
 * @property-read mixed $formatted_server_type
 * @property-read mixed $missing_token
 * @property-read mixed $whm_url
 * @method static \Database\Factories\ServerFactory factory(...$parameters)
 * @method static Builder|Server filter(\App\Filters\ServerFilters $filters)
 * @method static Builder|Server newModelQuery()
 * @method static Builder|Server newQuery()
 * @method static Builder|Server query()
 * @method static Builder|Server search($search)
 * @method static Builder|Server whereAddress($value)
 * @method static Builder|Server whereCreatedAt($value)
 * @method static Builder|Server whereId($value)
 * @method static Builder|Server whereName($value)
 * @method static Builder|Server whereNotes($value)
 * @method static Builder|Server wherePort($value)
 * @method static Builder|Server whereServerType($value)
 * @method static Builder|Server whereServerUpdatedAt($value)
 * @method static Builder|Server whereSettings($value)
 * @method static Builder|Server whereToken($value)
 * @method static Builder|Server whereUpdatedAt($value)
 * @method static Builder|Server withTokens()
 * @mixin \Eloquent
 */
class Server extends Model
{
    use HasFactory, ServerPresenter;

    protected $guarded = [];

    protected $withCount = ['accounts'];

    protected $casts = [
        'server_type' => ServerTypeEnum::class,
        'settings' => 'json',
        'server_updated_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_server_type',
        'formatted_backup_days',
        'formatted_disk_used',
        'formatted_disk_available',
        'formatted_disk_total',
        'formatted_php_version',
        'missing_token',
        'can_refresh_data',
        'whm_url',
    ];

    protected $hidden = ['token'];

    public static function refreshData()
    {
        $servers = static::where('server_type', '!=', 'reseller')
            ->orderBy('name')
            ->get();

        $servers->each(function ($server) {
            FetchServerDetails::dispatch($server);
            FetchServerAccounts::dispatch($server);
        });

        return true;
    }

    public function settings()
    {
        return new Settings($this, $this->settings);
    }

    public function fetchers()
    {
        return new Fetchers($this);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function addAccount($account)
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

    public function scopeWithTokens(Builder $query): void
    {
        $query->whereNotNull('token');
    }

    public function scopeFilter($query, ServerFilters $filters)
    {
        return $filters->apply($query);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('notes', 'LIKE', '%' . $search . '%');
        });
    }
}
