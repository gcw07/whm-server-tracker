<?php

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

test('the monitor listings can be filtered by having issues or not', function () {
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
