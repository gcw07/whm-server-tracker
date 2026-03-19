<?php

namespace App\Models;

use App\Enums\SslVhostTypeEnum;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $account_id
 * @property string $user
 * @property SslVhostTypeEnum $type
 * @property string $servername
 * @property array<array-key, mixed> $vhost_domains
 * @property array<array-key, mixed> $certificate_domains
 * @property CarbonImmutable|null $expires_at
 * @property string|null $issuer
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property-read Account $account
 *
 * @method static \Database\Factories\AccountSslCertificateFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereCertificateDomains($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereIssuer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereServername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountSslCertificate whereVhostDomains($value)
 *
 * @mixin \Eloquent
 */
#[Unguarded]
class AccountSslCertificate extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => SslVhostTypeEnum::class,
            'vhost_domains' => 'array',
            'certificate_domains' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
