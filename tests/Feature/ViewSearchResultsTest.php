<?php

use App\Livewire\Search;
use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests can not view search results page', function () {
    $this->get(route('search'))
        ->assertRedirect(route('login'));
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

    Livewire::test(Search::class)
        ->set('q', 'test')
        ->assertViewHas('servers', function ($servers) {
            return count($servers) === 1;
        })
        ->assertSee('MyTestServer.com')
        ->assertDontSee('NoResults.com');
});

test('the server notes can be searched', function () {
    Server::factory()->create(['notes' => 'see this note']);
    Server::factory()->create(['notes' => 'do not see me']);

    $this->actingAs($this->user);

    Livewire::test(Search::class)
        ->set('q', 'this')
        ->assertViewHas('servers', function ($servers) {
            return count($servers) === 1;
        })
        ->assertSee('this')
        ->assertDontSee('not');
});

test('the account domain can be searched', function () {
    Server::factory()->has(Account::factory()->count(2)->state(new Sequence(
        ['domain' => 'mytestsite.com'],
        ['domain' => 'never-see.com'],
    )))->create();

    $this->actingAs($this->user);

    Livewire::test(Search::class)
        ->set('q', 'test')
        ->assertViewHas('accounts', function ($accounts) {
            return count($accounts) === 1;
        })
        ->assertSee('mytestsite')
        ->assertDontSee('never-see');
});

test('the account ip can be searched', function () {
    Server::factory()->has(Account::factory()->count(2)->state(new Sequence(
        ['domain' => 'mytestsite.com', 'ip' => '255.1.1.100'],
        ['domain' => 'never-see.com', 'ip' => '192.1.1.100'],
    )))->create();

    $this->actingAs($this->user);

    Livewire::test(Search::class)
        ->set('q', '255')
        ->assertViewHas('accounts', function ($accounts) {
            return count($accounts) === 1;
        })
        ->assertSee('mytestsite.com')
        ->assertDontSee('never-see.com');
});

test('the account username can be searched', function () {
    Server::factory()->has(Account::factory()->count(2)->state(new Sequence(
        ['domain' => 'something.com', 'user' => 'mytest'],
        ['domain' => 'nope.com', 'user' => 'neversee'],
    )))->create();

    $this->actingAs($this->user);

    Livewire::test(Search::class)
        ->set('q', 'mytest')
        ->assertViewHas('accounts', function ($accounts) {
            return count($accounts) === 1;
        })
        ->assertSee('something.com')
        ->assertDontSee('nope.com');
});
