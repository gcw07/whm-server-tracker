<?php

namespace App\Models;

use App\Enums\DomainNameStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorDomainCheck extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => DomainNameStatusEnum::class,
            'expiration_date' => 'datetime',
            'nameservers' => 'array',
            'is_on_cloudflare' => 'boolean',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
