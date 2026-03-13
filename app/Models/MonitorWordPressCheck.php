<?php

namespace App\Models;

use App\Enums\WordPressStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorWordPressCheck extends Model
{
    protected $table = 'monitor_wordpress_checks';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'status' => WordPressStatusEnum::class,
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
