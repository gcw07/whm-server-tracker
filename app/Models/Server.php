<?php

namespace App\Models;

use App\Enums\ServerTypeEnum;
use App\Filters\ServerFilters;
use App\Jobs\FetchServerAccounts;
use App\Jobs\FetchServerDetails;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

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
    use HasFactory;

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

    public function getFormattedServerTypeAttribute()
    {
        return match ($this->server_type) {
            ServerTypeEnum::Dedicated => 'Dedicated',
            ServerTypeEnum::Reseller => 'Reseller',
            ServerTypeEnum::Vps => 'VPS',
        };
    }

    public function getFormattedBackupDaysAttribute()
    {
        if (! $this->settings()->backup_days) {
            return 'None';
        }

        return str_replace([0, 1, 2, 3, 4, 5, 6], ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], $this->settings()->backup_days);
    }

    public function getFormattedDiskUsedAttribute()
    {
        if (! $this->settings()->disk_used) {
            return 'Unknown';
        }

        return $this->formatFileSize($this->settings()->disk_used);
    }

    public function getFormattedDiskAvailableAttribute()
    {
        if (! $this->settings()->disk_available) {
            return 'Unknown';
        }

        return $this->formatFileSize($this->settings()->disk_available);
    }

    public function getFormattedDiskTotalAttribute()
    {
        if (! $this->settings()->disk_total) {
            return 'Unknown';
        }

        return $this->formatFileSize($this->settings()->disk_total);
    }

    public function getFormattedPhpVersionAttribute()
    {
        if (! $this->settings()->php_version) {
            return 'Unknown';
        }

        $versions = [
            'ea-php54' => 'PHP 5.4',
            'ea-php55' => 'PHP 5.5',
            'ea-php56' => 'PHP 5.6',
            'ea-php70' => 'PHP 7.0',
            'ea-php71' => 'PHP 7.1',
            'ea-php72' => 'PHP 7.2',
            'ea-php73' => 'PHP 7.3',
            'ea-php74' => 'PHP 7.4',
            'ea-php80' => 'PHP 8.0',
            'ea-php81' => 'PHP 8.1',
        ];

        return Arr::get($versions, $this->settings()->php_version, 'Unknown');
    }

    public function getMissingTokenAttribute()
    {
        if ($this->token === null) {
            return true;
        }

        return false;
    }

    public function getCanRefreshDataAttribute()
    {
        if ($this->server_type == 'reseller' || $this->missing_token) {
            return false;
        }

        return true;
    }

    public function getWhmUrlAttribute()
    {
        if ($this->port == 2087) {
            return "https://{$this->address}:{$this->port}";
        }

        return "http://{$this->address}:{$this->port}";
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = $this->trimTrailingZeroes(number_format($bytes / 1073741824, 2)) . ' TB';
        } elseif ($bytes >= 1048576) {
            $bytes = $this->trimTrailingZeroes(number_format($bytes / 1048576, 2)) . ' GB';
        } elseif ($bytes >= 1024) {
            $bytes = $this->trimTrailingZeroes(number_format($bytes / 1024, 2)) . ' MB';
        } else {
            $bytes = $bytes . ' KB';
        }

        return $bytes;
    }

    private function trimTrailingZeroes($number)
    {
        if (strpos($number, '.') !== false) {
            $number = rtrim($number, '0');
        }

        return rtrim($number, '.');
    }
}
