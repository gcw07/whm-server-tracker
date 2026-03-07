<?php

namespace App\Models;

use App\Enums\LighthouseStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorLighthouseCheck extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => LighthouseStatusEnum::class,
            'last_failed_at' => 'datetime',
            'last_succeeded_at' => 'datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
