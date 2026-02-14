<?php

namespace App\Models;

use App\Models\Presenters\AccountPresenter;
use Exception;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

/**
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
 * @property string|null $wordpress_version
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $backups_enabled
 * @property-read mixed $cpanel_url
 * @property-read mixed $domain_url
 * @property-read mixed $formatted_disk_usage
 * @property-read mixed $is_disk_critical
 * @property-read mixed $is_disk_full
 * @property-read mixed $is_disk_warning
 * @property-read \App\Models\Server $server
 *
 * @method static \Database\Factories\AccountFactory factory($count = null, $state = [])
 * @method static Builder<static>|Account filter(\App\Filters\AccountFilters $filters)
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
 * @method static Builder<static>|Account wherePlan($value)
 * @method static Builder<static>|Account whereServerId($value)
 * @method static Builder<static>|Account whereSetupDate($value)
 * @method static Builder<static>|Account whereSuspendReason($value)
 * @method static Builder<static>|Account whereSuspendTime($value)
 * @method static Builder<static>|Account whereSuspended($value)
 * @method static Builder<static>|Account whereUpdatedAt($value)
 * @method static Builder<static>|Account whereUser($value)
 * @method static Builder<static>|Account whereWordpressVersion($value)
 *
 * @mixin \Eloquent
 */
class Account extends Model
{
    use AccountPresenter, HasFactory;

    protected $guarded = [];

    protected $appends = [
        'domain_url',
        'cpanel_url',
        'formatted_disk_usage',
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
            'wordpress_version' => $this->wordpress_version,
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

    public function checkWordPress()
    {
        try {
            $url = $this->domain_url.'/feed/';
            $fetch = Http::get($url);

            if ($fetch->ok()) {
                $xml = simplexml_load_file($url);

                if ($xml === false) {
                    $this->setWordPressVersion(null);
                } else {
                    if ($xml->channel->generator) {
                        [, $version] = explode('?v=', $xml->channel->generator);
                        $this->setWordPressVersion($version);
                    }
                }
            } else {
                $this->setWordPressVersion(null);
            }
        } catch (Exception $exception) {
            $this->setWordPressVersion(null);
        }
    }

    public function setWordPressVersion($version): void
    {
        $this->wordpress_version = $version;
        $this->save();
    }
}
