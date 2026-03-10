<?php

namespace Database\Factories;

use App\Enums\SslVhostTypeEnum;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountSslCertificate>
 */
class AccountSslCertificateFactory extends Factory
{
    public function definition(): array
    {
        $domain = $this->faker->domainName();

        return [
            'account_id' => Account::factory(),
            'user' => explode('.', $domain)[0],
            'type' => $this->faker->randomElement(SslVhostTypeEnum::cases())->value,
            'servername' => $domain,
            'vhost_domains' => [$domain],
            'certificate_domains' => [$domain],
            'expires_at' => now()->addDays(90),
            'issuer' => "Let's Encrypt Authority X3",
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    public function expiringSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->addDays(14),
        ]);
    }
}
