<?php

use App\Models\Monitor;
use App\Models\MonitorWordPressCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

function createWordPressCheckMonitor(string $url, bool $enabled): Monitor
{
    MonitorFactory::new()->create(['url' => $url]);
    $monitor = Monitor::where('url', $url)->first();

    MonitorWordPressCheck::create([
        'monitor_id' => $monitor->id,
        'enabled' => $enabled,
    ]);

    return $monitor;
}

test('guests can not view the wp updates report', function () {
    $this->get(route('reports.wp-updates'))
        ->assertRedirectToRoute('login');
});

test('monitors with an enabled wordpress check are shown', function () {
    createWordPressCheckMonitor('https://wp-check-enabled.com', enabled: true);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::report.wp-updates')
        ->assertSee('wp-check-enabled.com');
});

test('monitors with a disabled wordpress check are hidden', function () {
    createWordPressCheckMonitor('https://wp-check-disabled.com', enabled: false);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::report.wp-updates')
        ->assertDontSee('wp-check-disabled.com');
});
