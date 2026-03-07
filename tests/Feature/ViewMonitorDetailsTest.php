<?php

use App\Models\Account;
use App\Models\Monitor;
use App\Models\MonitorBlacklistCheck;
use App\Models\MonitorDomainCheck;
use App\Models\MonitorLighthouseCheck;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

test('guests can not view monitor details page', function () {
    $monitor = MonitorFactory::new()->create(['url' => 'https://myserver.com']);

    $this->get(route('monitors.show', $monitor->id))
        ->assertRedirectToRoute('login');
});

test('an authorized user can view monitor details page', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->assertSee('myserver.com');
});

test('an authorized user can turn off uptime checks from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'uptime_check_enabled' => true,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->call('toggleUptimeCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertFalse($monitor->uptime_check_enabled);
    });
});

test('an authorized user can turn on uptime checks from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'uptime_check_enabled' => false,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->call('toggleUptimeCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertTrue($monitor->uptime_check_enabled);
    });
});

test('an authorized user can turn off ssl certificate checks from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'certificate_check_enabled' => true,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->call('toggleCertificateCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertFalse($monitor->certificate_check_enabled);
    });
});

test('an authorized user can turn on ssl certificate checks from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create([
        'url' => 'https://myserver.com',
        'certificate_check_enabled' => false,
    ]);

    $monitor = Monitor::first();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->call('toggleCertificateCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertTrue($monitor->certificate_check_enabled);
    });
});

test('an authorized user can toggle blacklist check from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorBlacklistCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->call('toggleBlacklistCheck');

    $this->assertFalse((bool) $monitor->blacklistCheck()->value('enabled'));
});

test('an authorized user can toggle lighthouse check from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorLighthouseCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->call('toggleLighthouseCheck');

    $this->assertFalse((bool) $monitor->lighthouseCheck()->value('enabled'));
});

test('an authorized user can toggle domain name check from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorDomainCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor])
        ->call('toggleDomainNameExpirationCheck');

    $this->assertFalse((bool) $monitor->domainCheck()->value('enabled'));
});

test('monitor observer creates check records when a monitor is created via App model', function () {
    $monitor = Monitor::create([
        'url' => 'https://myserver.com',
        'uptime_check_enabled' => true,
        'certificate_check_enabled' => false,
    ]);

    $this->assertNotNull($monitor->blacklistCheck);
    $this->assertNotNull($monitor->lighthouseCheck);
    $this->assertNotNull($monitor->domainCheck);
});
