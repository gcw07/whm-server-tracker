<?php

namespace Database\Factories;

use App\Models\Server;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Server::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->domainName,
            'address' => $this->faker->ipv4,
            'port' => '2087',
            'server_type' => $this->faker->randomElement(['vps', 'dedicated', 'reseller']),
            'settings' => [],
        ];
    }
}
