<?php

namespace App\Models;

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
 * @property int|null $disk_quota
 * @property float $disk_used_percent
 * @property bool $suspended_incoming
 * @property bool $suspended_login
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Account $account
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
class AccountEmail extends Model
{
    /** @use HasFactory<\Database\Factories\AccountEmailFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'suspended_incoming' => 'boolean',
            'suspended_login' => 'boolean',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
