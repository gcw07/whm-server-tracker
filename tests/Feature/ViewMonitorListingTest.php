<?php

use App\Models\Account;
use App\Models\AccountSslCertificate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;
use Spatie\UptimeMonitor\Models\Enums\UptimeStatus;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests can not view monitor listings page', function () {
    $this->get(route('monitors.index'))
        ->assertRedirectToRoute('login');
});

test('an authorized user can view monitor listings page', function () {
    MonitorFactory::new()->create(['url' => 'https://myserver.com']);
    MonitorFactory::new()->create(['url' => 'https://otherserver.com']);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.listings')
        ->assertCount('monitors', 2)
        ->assertSee('myserver.com');
});

test('the monitor listings are in alphabetical order', function () {
    MonitorFactory::new()->count(3)->state(new Sequence(
        ['url' => 'https://someserver.com'],
        ['url' => 'https://anotherserver.com'],
        ['url' => 'https://thelastserver.com'],
    ))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.listings')
        ->assertSeeInOrder(['anotherserver.com', 'someserver.com', 'thelastserver.com']);
});

test('the monitor listings can be filtered by having uptime issues', function () {
    MonitorFactory::new()->count(3)->state(new Sequence(
        ['url' => 'https://someserver.com', 'uptime_status' => UptimeStatus::DOWN],
        ['url' => 'https://anotherserver.com', 'uptime_status' => UptimeStatus::DOWN],
        ['url' => 'https://thelastserver.com', 'uptime_status' => UptimeStatus::UP],
    ))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.listings')
        ->set('monitorType', 'issues')
        ->assertcount('monitors', 2)
        ->assertSee('https://someserver.com');
});

test('the monitor listings issues filter includes monitors with expired ssl certificates', function () {
    $monitor = MonitorFactory::new()->create(['url' => 'https://someserver.com', 'uptime_status' => UptimeStatus::UP, 'certificate_check_enabled' => true]);
    $healthyMonitor = MonitorFactory::new()->create(['url' => 'https://healthyserver.com', 'uptime_status' => UptimeStatus::UP]);

    $account = Account::factory()->create(['monitor_id' => $monitor->id]);
    AccountSslCertificate::factory()->expired()->create(['account_id' => $account->id]);

    $healthyAccount = Account::factory()->create(['monitor_id' => $healthyMonitor->id]);
    AccountSslCertificate::factory()->create(['account_id' => $healthyAccount->id, 'expires_at' => now()->addDays(60)]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.listings')
        ->set('monitorType', 'issues')
        ->assertCount('monitors', 1)
        ->assertSee('someserver.com')
        ->assertDontSee('healthyserver.com');
});

test('the monitor listings issues filter includes monitors with ssl certificates expiring soon', function () {
    $monitor = MonitorFactory::new()->create(['url' => 'https://expiringsoon.com', 'uptime_status' => UptimeStatus::UP, 'certificate_check_enabled' => true]);
    $healthyMonitor = MonitorFactory::new()->create(['url' => 'https://healthyserver.com', 'uptime_status' => UptimeStatus::UP]);

    $account = Account::factory()->create(['monitor_id' => $monitor->id]);
    AccountSslCertificate::factory()->expiringSoon()->create(['account_id' => $account->id]);

    $healthyAccount = Account::factory()->create(['monitor_id' => $healthyMonitor->id]);
    AccountSslCertificate::factory()->create(['account_id' => $healthyAccount->id, 'expires_at' => now()->addDays(60)]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.listings')
        ->set('monitorType', 'issues')
        ->assertCount('monitors', 1)
        ->assertSee('expiringsoon.com')
        ->assertDontSee('healthyserver.com');
});

test('the monitor listings issues filter excludes monitors with expired ssl certificates on suspended accounts', function () {
    $monitor = MonitorFactory::new()->create(['url' => 'https://suspended.com', 'uptime_status' => UptimeStatus::UP, 'certificate_check_enabled' => true]);

    $account = Account::factory()->create(['monitor_id' => $monitor->id, 'suspended' => true]);
    AccountSslCertificate::factory()->expired()->create(['account_id' => $account->id]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.listings')
        ->set('monitorType', 'issues')
        ->assertCount('monitors', 0);
});

test('the monitor listings issues filter excludes monitors with expiring ssl certificates when certificate check is disabled', function () {
    $monitor = MonitorFactory::new()->create([
        'url' => 'https://certdisabled.com',
        'uptime_status' => UptimeStatus::UP,
        'certificate_check_enabled' => false,
    ]);

    $account = Account::factory()->create(['monitor_id' => $monitor->id]);
    AccountSslCertificate::factory()->expiringSoon()->create(['account_id' => $account->id]);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::monitor.listings')
        ->set('monitorType', 'issues')
        ->assertCount('monitors', 0);
});
