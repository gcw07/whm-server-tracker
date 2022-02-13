<?php

namespace Database\Factories;

use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    public function definition(): array
    {
        $domain = $this->faker->domainName;
        $user = explode('.', $domain)[0];
        $suspended = $this->faker->boolean(10);

        return [
            'server_id' => Server::factory(),
            'domain' => $domain,
            'user' => $user,
            'ip' => $this->faker->ipv4,
            'backup' => $this->faker->boolean(90),
            'suspended' => $suspended,
            'suspend_reason' => $suspended ? 'account unpaid' : 'not suspended',
            'suspend_time' => $suspended ? Carbon::now()->subDays(15) : null,
            'setup_date' => $this->faker->dateTimeBetween('-1 year', '-2 weeks'),
            'disk_used' => $this->faker->randomElement(['250M', '500M', '800M', '950M']),
            'disk_limit' => $this->faker->randomElement(['1000M', '3000M', '5000M', '10000M']),
            'plan' => $this->faker->randomElement(['1 Gig', '3 Gig', '5 Gig', '10 Gig']),
        ];
    }
}
