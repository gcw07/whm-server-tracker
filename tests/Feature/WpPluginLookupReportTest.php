<?php

use App\Models\Monitor;
use App\Models\MonitorWordPressCheck;
use App\Models\MonitorWpPlugin;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

function createAgentWordPressMonitor(string $url): Monitor
{
    MonitorFactory::new()->create(['url' => $url]);
    $monitor = Monitor::where('url', $url)->first();

    MonitorWordPressCheck::create([
        'monitor_id' => $monitor->id,
        'enabled' => true,
        'check_source' => 'agent',
    ]);

    return $monitor;
}

test('guests can not view the wp plugin lookup report', function () {
    $this->get(route('reports.wp-plugin-lookup'))
        ->assertRedirectToRoute('login');
});

test('no plugin selected shows the empty prompt', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test('pages::report.wp-plugin-lookup')
        ->assertSee('Select a plugin above to see results.');
});

test('selecting a plugin shows sites that have it installed', function () {
    $withPlugin = createAgentWordPressMonitor('https://has-plugin.com');
    MonitorWpPlugin::factory()->create([
        'monitor_id' => $withPlugin->id,
        'name' => 'Wordfence Security',
        'version' => '8.1.2',
        'active' => true,
    ]);

    $withoutPlugin = createAgentWordPressMonitor('https://no-plugin.com');

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::report.wp-plugin-lookup')
        ->set('selectedPlugin', 'Wordfence Security')
        ->assertSee('has-plugin.com')
        ->assertSee('8.1.2')
        ->assertDontSee('no-plugin.com');
});

test('switching to missing view only shows agent-checked wp sites without the plugin', function () {
    $withPlugin = createAgentWordPressMonitor('https://has-plugin.com');
    MonitorWpPlugin::factory()->create([
        'monitor_id' => $withPlugin->id,
        'name' => 'Wordfence Security',
    ]);

    createAgentWordPressMonitor('https://missing-plugin.com');

    MonitorFactory::new()->create(['url' => 'https://rss-only.com']);
    $rssMonitor = Monitor::where('url', 'https://rss-only.com')->first();
    MonitorWordPressCheck::create([
        'monitor_id' => $rssMonitor->id,
        'enabled' => true,
        'check_source' => 'rss',
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::report.wp-plugin-lookup')
        ->set('selectedPlugin', 'Wordfence Security')
        ->call('switchView', 'missing')
        ->assertSee('missing-plugin.com')
        ->assertDontSee('has-plugin.com')
        ->assertDontSee('rss-only.com');
});
