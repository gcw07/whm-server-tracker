<?php

use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Database\Factories\MonitorFactory;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests can not view search results page', function () {
    $this->get(route('search'))
        ->assertRedirectToRoute('login');
});

test('an authorized user can view search results page', function () {
    $this->actingAs($this->user)
        ->get(route('search'))
        ->assertSuccessful();
});

test('the server name can be searched', function () {
    Server::factory()->create(['name' => 'MyTestServer.com']);
    Server::factory()->create(['name' => 'NoResults.com']);

    $this->actingAs($this->user);

    Livewire::test('pages::search')
        ->set('search', 'test')
        ->assertCount('servers', 1)
        ->assertSee('MyTestServer.com')
        ->assertDontSee('NoResults.com');
});

test('the server address can be searched', function () {
    Server::factory()->create(['name' => 'MyTestServer.com', 'address' => '1.1.1.1']);
    Server::factory()->create(['name' => 'NoResults.com', 'address' => '2.2.2.2']);

    $this->actingAs($this->user);

    Livewire::test('pages::search')
        ->set('search', '1.1.1')
        ->assertCount('servers', 1)
        ->assertSee('MyTestServer.com')
        ->assertDontSee('NoResults.com');
});

test('the server notes can be searched', function () {
    Server::factory()->create(['notes' => 'magic sully']);
    Server::factory()->create(['notes' => 'big world']);

    $this->actingAs($this->user);

    Livewire::test('pages::search')
        ->set('search', 'magic')
        ->assertCount('servers', 1)
        ->assertSee('magic')
        ->assertDontSee('big');
});

test('the account domain can be searched', function () {
    Server::factory()->has(Account::factory()->count(2)->state(new Sequence(
        ['domain' => 'mytestsite.com'],
        ['domain' => 'never-see.com'],
    )))->create();

    $this->actingAs($this->user);

    Livewire::test('pages::search')
        ->set('search', 'test')
        ->assertCount('accounts', 1)
        ->assertSee('mytestsite')
        ->assertDontSee('never-see');
});

test('the account ip can be searched', function () {
    Server::factory()->has(Account::factory()->count(2)->state(new Sequence(
        ['domain' => 'mytestsite.com', 'ip' => '255.1.1.100'],
        ['domain' => 'never-see.com', 'ip' => '192.1.1.100'],
    )))->create();

    $this->actingAs($this->user);

    Livewire::test('pages::search')
        ->set('search', '255')
        ->assertCount('accounts', 1)
        ->assertSee('mytestsite.com')
        ->assertDontSee('never-see.com');
});

test('the account username can be searched', function () {
    Server::factory()->has(Account::factory()->count(2)->state(new Sequence(
        ['domain' => 'something.com', 'user' => 'mytest'],
        ['domain' => 'nope.com', 'user' => 'neversee'],
    )))->create();

    $this->actingAs($this->user);

    Livewire::test('pages::search')
        ->set('search', 'mytest')
        ->assertCount('accounts', 1)
        ->assertSee('something.com')
        ->assertDontSee('nope.com');
});

test('the monitor url can be searched', function () {
    MonitorFactory::new()->create(['url' => 'https://something.com']);
    MonitorFactory::new()->create(['url' => 'https://pizza.com']);

    $this->actingAs($this->user);

    Livewire::test('pages::search')
        ->set('search', 'something')
        ->assertCount('monitors', 1)
        ->assertSee('something.com')
        ->assertDontSee('pizza.com');
});
