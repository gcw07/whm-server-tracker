<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\UptimeMonitor\Models\Monitor;

class LighthouseAudit extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
    ];

    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }
}
