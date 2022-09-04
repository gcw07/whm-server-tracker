<?php

namespace Database\Factories;

use App\Models\Monitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class DowntimeStatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'monitor_id' => Monitor::factory(),
            'date' => $this->faker->date(),
            'downtime_period' => $this->faker->numberBetween(0, 3600),
        ];
    }
}
