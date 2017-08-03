<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    protected $guarded = [];
    protected $casts = ['backup_enabled' => 'boolean'];
    protected $dates = ['details_last_updated', 'accounts_last_updated'];

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
