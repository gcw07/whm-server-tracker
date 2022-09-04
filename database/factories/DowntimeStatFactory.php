<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

class DowntimeStatFactory extends Factory
{
    public function definition(): array
    {
        return [
            'monitor_id' => MonitorFactory::new(),
            'date' => $this->faker->date(),
            'downtime_period' => $this->faker->numberBetween(0, 3600),
        ];
    }
}
