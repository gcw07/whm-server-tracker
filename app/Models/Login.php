<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Login
 *
 * @property int $id
 * @property int $user_id
 * @property string $ip_address
 * @property string $created_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Login newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Login newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Login query()
 * @method static \Illuminate\Database\Eloquent\Builder|Login whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Login whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Login whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Login whereUserId($value)
 * @mixin \Eloquent
 */
class Login extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
