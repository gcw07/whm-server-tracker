<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Login whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Login extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
