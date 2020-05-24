<?php

namespace App\Models\Concerns;

use App\Models\Login;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasLogins
{
    public function logins(): HasMany
    {
        return $this->hasMany(Login::class);
    }

    public function lastLogin(): BelongsTo
    {
        return $this->belongsTo(Login::class);
    }

    public function scopeWithLastLogin(Builder $query): void
    {
        $query->addSelect([
            'last_login_id' => Login::select('id')
                ->whereColumn('user_id', 'users.id')
                ->latest()
                ->limit(1)
        ])->with('lastLogin');
    }
}
