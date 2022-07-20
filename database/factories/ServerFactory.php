<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->domainName,
            'address' => $this->faker->ipv4,
            'port' => '2087',
            'server_type' => $this->faker->randomElement(['vps', 'dedicated', 'reseller']),
            'settings' => null,
        ];
    }
}
