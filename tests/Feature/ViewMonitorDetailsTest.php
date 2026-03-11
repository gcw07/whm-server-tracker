<?php

use App\Models\Account;
use App\Models\AccountSslCertificate;
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

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
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

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
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

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->call('toggleUptimeCheck');

    tap($monitor->fresh(), function (Monitor $monitor) {
        $this->assertTrue($monitor->uptime_check_enabled);
    });
});

test('ssl certificates from accounts are displayed on monitor details', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $account = Account::factory()->create(['domain' => 'myserver.com', 'monitor_id' => $monitor->id]);

    AccountSslCertificate::factory()->create([
        'account_id' => $account->id,
        'servername' => 'myserver.com',
        'type' => 'main',
        'issuer' => "Let's Encrypt Authority X3",
        'expires_at' => now()->addDays(90),
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->assertSee('myserver.com')
        ->assertSee("Let's Encrypt Authority X3")
        ->assertSee('Main');
});

test('a valid ssl certificate shows valid until on monitor details', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $account = Account::factory()->create(['domain' => 'myserver.com', 'monitor_id' => $monitor->id]);

    AccountSslCertificate::factory()->create([
        'account_id' => $account->id,
        'servername' => 'myserver.com',
        'expires_at' => now()->addDays(90),
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->assertSee('Valid until');
});

test('an expiring soon ssl certificate shows expires in on monitor details', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $account = Account::factory()->create(['domain' => 'myserver.com', 'monitor_id' => $monitor->id]);

    AccountSslCertificate::factory()->expiringSoon()->create([
        'account_id' => $account->id,
        'servername' => 'myserver.com',
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->assertSee('Expires in');
});

test('an expired ssl certificate shows expired on monitor details', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $account = Account::factory()->create(['domain' => 'myserver.com', 'monitor_id' => $monitor->id]);

    AccountSslCertificate::factory()->expired()->create([
        'account_id' => $account->id,
        'servername' => 'myserver.com',
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->assertSee('Expired');
});

test('monitor details shows no ssl certificates found when there are none', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    Account::factory()->create(['domain' => 'myserver.com', 'monitor_id' => $monitor->id]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->assertSee('No SSL certificates found.');
});

test('covered vhost domains are shown with a check on monitor details', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $account = Account::factory()->create(['domain' => 'myserver.com', 'monitor_id' => $monitor->id]);

    AccountSslCertificate::factory()->create([
        'account_id' => $account->id,
        'servername' => 'myserver.com',
        'vhost_domains' => ['myserver.com', 'www.myserver.com'],
        'certificate_domains' => ['myserver.com', 'www.myserver.com'],
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->assertSee('myserver.com')
        ->assertSee('www.myserver.com');
});

test('uncovered vhost domains are shown differently on monitor details', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    $account = Account::factory()->create(['domain' => 'myserver.com', 'monitor_id' => $monitor->id]);

    AccountSslCertificate::factory()->create([
        'account_id' => $account->id,
        'servername' => 'myserver.com',
        'vhost_domains' => ['myserver.com', 'sub.myserver.com'],
        'certificate_domains' => ['myserver.com'],
    ]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->assertSee('myserver.com')
        ->assertSee('sub.myserver.com');
});

test('an authorized user can toggle blacklist check from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorBlacklistCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->call('toggleBlacklistCheck');

    $this->assertFalse((bool) $monitor->blacklistCheck()->value('enabled'));
});

test('an authorized user can toggle lighthouse check from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorLighthouseCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
        ->call('toggleLighthouseCheck');

    $this->assertFalse((bool) $monitor->lighthouseCheck()->value('enabled'));
});

test('an authorized user can toggle domain name check from monitor details', function () {
    Account::factory()->create(['domain' => 'myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    $monitor = Monitor::first();
    MonitorDomainCheck::create(['monitor_id' => $monitor->id, 'enabled' => true]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.details', ['monitor' => $monitor->id])
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
