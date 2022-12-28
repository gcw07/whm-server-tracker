<?php

namespace App\Models;

use App\Casts\Lower;
use App\Casts\Notifications;
use App\Models\Concerns\HasLogins;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property Lower $email
 * @property string $password
 * @property string|null $remember_token
 * @property \App\Collections\NotificationsCollection|null $notification_types
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Login|null $lastLogin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Login[] $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 *
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static Builder|User forNotificationType($type)
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User whereNotificationTypes($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static Builder|User withLastLogin()
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasLogins, HasFactory, Notifiable;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email' => Lower::class,
        'notification_types' => Notifications::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function scopeForNotificationType(Builder $query, $type): void
    {
        $query->where("notification_types->$type", true);
    }
}
