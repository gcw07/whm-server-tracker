<?php

namespace Database\Factories;

use App\Models\MonitorWpPlugin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

/**
 * @extends Factory<MonitorWpPlugin>
 */
class MonitorWpPluginFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'monitor_id' => MonitorFactory::new(),
            'name' => $name,
            'file' => Str::slug($name).'/plugin.php',
            'version' => $this->faker->numerify('#.#.#'),
            'active' => true,
            'update_available' => false,
        ];
    }
}
