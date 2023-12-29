<?php

use App\Livewire\Monitor\Details as MonitorDetails;
use App\Models\Monitor;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

test('guests can not view monitor details page', function () {
    $monitor = MonitorFactory::new()->create(['url' => 'https://myserver.com']);

    $this->get(route('monitors.show', $monitor->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can view monitor details page', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test(MonitorDetails::class, ['monitor' => $monitor])
        ->assertSee('myserver.com');
});

test('an authorized user can turn off uptime checks from monitor details', function () {
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'uptime_check_enabled' => true,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test(MonitorDetails::class, ['monitor' => $monitor])
        ->call('toggleUptimeCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertFalse($monitor->uptime_check_enabled);
    });
});

test('an authorized user can turn on uptime checks from monitor details', function () {
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'uptime_check_enabled' => false,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test(MonitorDetails::class, ['monitor' => $monitor])
        ->call('toggleUptimeCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertTrue($monitor->uptime_check_enabled);
    });
});

test('an authorized user can turn off ssl certificate checks from monitor details', function () {
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'certificate_check_enabled' => true,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test(MonitorDetails::class, ['monitor' => $monitor])
        ->call('toggleCertificateCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertFalse($monitor->certificate_check_enabled);
    });
});

test('an authorized user can turn on ssl certificate checks from monitor details', function () {
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'certificate_check_enabled' => false,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test(MonitorDetails::class, ['monitor' => $monitor])
        ->call('toggleCertificateCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertTrue($monitor->certificate_check_enabled);
    });
});
