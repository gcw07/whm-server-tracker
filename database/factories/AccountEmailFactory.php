<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountEmail>
 */
class AccountEmailFactory extends Factory
{
    public function definition(): array
    {
        $domain = $this->faker->domainName();
        $user = $this->faker->userName();

        return [
            'account_id' => Account::factory(),
            'email' => "{$user}@{$domain}",
            'user' => $user,
            'domain' => $domain,
            'disk_used' => $this->faker->numberBetween(0, 500_000_000),
            'disk_quota' => $this->faker->optional(0.8)->numberBetween(500_000_000, 2_000_000_000),
            'disk_used_percent' => $this->faker->randomFloat(2, 0, 100),
            'suspended_incoming' => $this->faker->boolean(5),
            'suspended_login' => $this->faker->boolean(5),
        ];
    }
}
