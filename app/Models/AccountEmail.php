<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Database\Factories\AccountEmailFactory;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property string $email
 * @property string $user
 * @property string $domain
 * @property int $disk_used
 * @property int $disk_quota
 * @property float $disk_used_percent
 * @property bool $suspended_incoming
 * @property bool $suspended_login
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Account $account
 * @property-read mixed $formatted_disk_quota
 * @property-read mixed $formatted_disk_used
 *
 * @method static \Database\Factories\AccountEmailFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereDiskQuota($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereDiskUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereDiskUsedPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereSuspendedIncoming($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereSuspendedLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountEmail whereUser($value)
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class AccountEmail extends Model
{
    /** @use HasFactory<AccountEmailFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'suspended_incoming' => 'boolean',
            'suspended_login' => 'boolean',
        ];
    }

    protected function formattedDiskUsed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->formatBytes($this->disk_used),
        );
    }

    protected function formattedDiskQuota(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->disk_quota !== 0 ? $this->formatBytes($this->disk_quota) : 'Unlimited',
        );
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $precision = [0, 0, 1, 2, 2];

        for ($i = 0; ($bytes / 1024) >= 0.9 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision[$i]).' '.$units[$i];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
