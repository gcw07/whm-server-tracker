<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'server_settings';
    protected $guarded = [];
    public $timestamps = false;
}
