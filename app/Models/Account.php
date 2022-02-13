<?php

namespace App\Models;

use App\Filters\AccountFilters;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Account
 *
 * @property int $id
 * @property int $server_id
 * @property string $domain
 * @property string $user
 * @property string $ip
 * @property bool $backup
 * @property bool $suspended
 * @property string $suspend_reason
 * @property \Illuminate\Support\Carbon|null $suspend_time
 * @property \Illuminate\Support\Carbon|null $setup_date
 * @property string $disk_used
 * @property string $disk_limit
 * @property string $plan
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $cpanel_url
 * @property-read mixed $disk_usage
 * @property-read mixed $disk_usage_raw
 * @property-read mixed $domain_url
 * @property-read mixed $whm_url
 * @property-read \App\Models\Server $server
 * @method static \Database\Factories\AccountFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Account filter(\App\Filters\AccountFilters $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account search($search)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereBackup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDiskLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDiskUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePlan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSetupDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSuspendReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSuspendTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSuspended($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUser($value)
 * @mixin \Eloquent
 */
class Account extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = ['backup' => 'boolean', 'suspended' => 'boolean'];
    protected $dates = ['suspend_time', 'setup_date'];
    protected $appends = ['disk_usage', 'disk_usage_raw', 'cpanel_url', 'whm_url', 'domain_url'];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function scopeFilter($query, AccountFilters $filters)
    {
        return $filters->apply($query);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('domain', 'LIKE', '%' . $search . '%')
                ->orWhere('user', 'LIKE', '%' . $search . '%')
                ->orWhere('ip', 'LIKE', '%' . $search . '%');
        });
    }

    public function getDiskUsageAttribute()
    {
        if (is_null($this->disk_usage_raw)) {
            return 'n/a';
        }

        return $this->disk_usage_raw . '%';
    }

    public function getDiskUsageRawAttribute()
    {
        $diskUsed = substr($this->disk_used, 0, -1);
        $diskLimit = substr($this->disk_limit, 0, -1);

        if (! is_numeric($diskLimit)) {
            return null;
        }

        return round(($diskUsed / $diskLimit) * 100, 1);
    }

    public function getCpanelUrlAttribute()
    {
        return "https://{$this->domain}/cpanel";
    }

    public function getWhmUrlAttribute()
    {
        if ($this->server->port == 2087) {
            return "https://{$this->server->address}:{$this->server->port}";
        }

        return "http://{$this->server->address}:{$this->server->port}";
    }

    public function getDomainUrlAttribute()
    {
        return "http://{$this->domain}";
    }
}
