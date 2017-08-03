<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

    public function server()
    {
        return $this->belongsTo(Server::class);
    }
}
