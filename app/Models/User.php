<?php

namespace App\Models;

use App\Casts\Lower;
use App\Models\Concerns\HasLogins;
use App\Models\Concerns\Unguarded;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasLogins, Notifiable, Unguarded;

    protected $casts = ['email' => Lower::class,];
    protected $hidden = ['password', 'remember_token'];
}
