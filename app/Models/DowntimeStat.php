<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Models\Monitor;

class DowntimeStat extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'date' => 'date',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
