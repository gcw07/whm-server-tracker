<?php

use App\Livewire\Account\Listings as AccountListings;
use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests can not view account listings page', function () {
    $this->get(route('accounts.index'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view account listings page', function () {
    Server::factory()->has(Account::factory()->state([
        'domain' => 'mytestsite.com',
        'ip' => '255.1.1.100',
    ]))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(AccountListings::class)
        ->assertViewHas('accounts', function ($accounts) {
            return count($accounts) === 1;
        })
        ->assertSee('mytestsite.com');
});

test('the account listings are in alphabetical order', function () {
    Server::factory()->has(Account::factory()->count(3)->state(new Sequence(
        ['domain' => 'somesite.com'],
        ['domain' => 'anothersite.com'],
        ['domain' => 'thelastsite.com'],
    )))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test(AccountListings::class)
        ->assertViewHas('accounts', function ($accounts) {
            return count($accounts) === 3;
        });
})->skip();
