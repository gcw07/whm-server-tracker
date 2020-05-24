<?php

namespace App\Models;

use Domain\Model;

class Login extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
