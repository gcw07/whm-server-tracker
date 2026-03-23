<?php

namespace App\Models;

use App\Models\Presenters\AccountPresenter;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $server_id
 * @property int|null $monitor_id
 * @property string $domain
 * @property string $user
 * @property string $ip
 * @property bool $backup
 * @property bool $suspended
 * @property string $suspend_reason
 * @property CarbonImmutable|null $suspend_time
 * @property CarbonImmutable|null $setup_date
 * @property string $disk_used
 * @property string $disk_limit
 * @property string $plan
 * @property string|null $php_version
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read mixed $backups_enabled
 * @property-read mixed $cpanel_url
 * @property-read mixed $domain_url
 * @property-read Collection<int, AccountEmail> $emails
 * @property-read int|null $emails_count
 * @property-read mixed $formatted_disk_usage
 * @property-read mixed $is_disk_critical
 * @property-read mixed $is_disk_full
 * @property-read mixed $is_disk_warning
 * @property-read Monitor|null $monitor
 * @property-read Server $server
 * @property-read Collection<int, AccountSslCertificate> $sslCertificates
 * @property-read int|null $ssl_certificates_count
 *
 * @method static \Database\Factories\AccountFactory factory($count = null, $state = [])
 * @method static Builder<static>|Account newModelQuery()
 * @method static Builder<static>|Account newQuery()
 * @method static Builder<static>|Account query()
 * @method static Builder<static>|Account search(string $term)
 * @method static Builder<static>|Account whereBackup($value)
 * @method static Builder<static>|Account whereCreatedAt($value)
 * @method static Builder<static>|Account whereDiskLimit($value)
 * @method static Builder<static>|Account whereDiskUsed($value)
 * @method static Builder<static>|Account whereDomain($value)
 * @method static Builder<static>|Account whereId($value)
 * @method static Builder<static>|Account whereIp($value)
 * @method static Builder<static>|Account whereMonitorId($value)
 * @method static Builder<static>|Account wherePlan($value)
 * @method static Builder<static>|Account whereServerId($value)
 * @method static Builder<static>|Account whereSetupDate($value)
 * @method static Builder<static>|Account whereSuspendReason($value)
 * @method static Builder<static>|Account whereSuspendTime($value)
 * @method static Builder<static>|Account whereSuspended($value)
 * @method static Builder<static>|Account whereUpdatedAt($value)
 * @method static Builder<static>|Account whereUser($value)
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class Account extends Model
{
    use AccountPresenter, HasFactory;

    protected $appends = [
        'domain_url',
        'cpanel_url',
        'formatted_disk_usage',
        'formatted_php_version',
        'backups_enabled',
        'is_disk_warning',
        'is_disk_critical',
        'is_disk_full',
    ];

    protected function casts(): array
    {
        return [
            'backup' => 'boolean',
            'suspended' => 'boolean',
            'suspend_time' => 'datetime',
            'setup_date' => 'datetime',
        ];
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(AccountEmail::class);
    }

    public function sslCertificates(): HasMany
    {
        return $this->hasMany(AccountSslCertificate::class);
    }

    public function export($columns): array
    {
        return collect([
            'domain' => $this->domain,
            'server' => $this->server->name,
            'username' => $this->user,
            'ip' => $this->ip,
            'backups' => $this->backup,
            'suspended' => $this->suspended,
            'suspended_reason' => $this->suspend_reason,
            'suspended_time' => $this->suspend_time?->format('Y-m-d H:i:s'),
            'setup_date' => $this->setup_date?->format('Y-m-d H:i:s'),
            'disk_used' => $this->disk_used,
            'disk_limit' => $this->disk_limit,
            'disk_usage' => $this->formatted_disk_usage,
            'plan' => $this->plan,
        ])->only($columns)->all();
    }

    #[Scope]
    public function search(Builder $query, string $term): void
    {
        $query->whereAny([
            'domain',
            'user',
            'ip',
        ], 'LIKE', "%$term%");
    }
}
