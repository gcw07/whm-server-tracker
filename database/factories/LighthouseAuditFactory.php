<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

class LighthouseAuditFactory extends Factory
{
    public function definition(): array
    {
        return [
            'monitor_id' => MonitorFactory::new(),
            'performance_score' => $this->faker->numberBetween(0, 100),
            'accessibility_score' => $this->faker->numberBetween(0, 100),
            'best_practices_score' => $this->faker->numberBetween(0, 100),
            'seo_score' => $this->faker->numberBetween(0, 100),
            'speed_index' => $this->faker->numberBetween(500, 8000),
            'first_contentful_paint' => $this->faker->numberBetween(300, 5000),
            'largest_contentful_paint' => $this->faker->numberBetween(500, 7000),
            'time_to_interactive' => $this->faker->numberBetween(800, 10000),
            'total_blocking_time' => $this->faker->numberBetween(0, 1000),
            'cumulative_layout_shift' => round($this->faker->randomFloat(3, 0, 0.5), 3),
            'form_factor' => $this->faker->randomElement(['mobile', 'desktop']),
        ];
    }
}
