<?php

namespace App\Models;

use App\Enums\SslVhostTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccountSslCertificate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => SslVhostTypeEnum::class,
            'domains' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
