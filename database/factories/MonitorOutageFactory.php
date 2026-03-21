<?php

namespace Database\Factories;

use App\Models\MonitorOutage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

/**
 * @extends Factory<MonitorOutage>
 */
class MonitorOutageFactory extends Factory
{
    public function definition(): array
    {
        $startedAt = Carbon::instance($this->faker->dateTimeThisMonth());
        $endedAt = $startedAt->copy()->addSeconds($this->faker->numberBetween(60, 3600));

        return [
            'monitor_id' => MonitorFactory::new(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'duration_seconds' => $endedAt->diffInSeconds($startedAt),
            'created_at' => $endedAt,
        ];
    }
}
