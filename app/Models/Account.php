<?php

namespace App\Models;

use App\Filters\AccountFilters;
use App\Models\Presenters\AccountPresenter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    use HasFactory, AccountPresenter;

    protected $guarded = [];
    protected $casts = ['backup' => 'boolean', 'suspended' => 'boolean'];
    protected $dates = ['suspend_time', 'setup_date'];
    protected $appends = [
        'domain_url',
        'cpanel_url',
        'formatted_disk_usage',
        'backups_enabled',
        'is_disk_warning',
        'is_disk_critical',
        'is_disk_full',
    ];

    public function server(): BelongsTo
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
}
