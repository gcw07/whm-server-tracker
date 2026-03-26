<?php

use App\Models\Account;
use App\Models\AccountEmail;
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
        ->assertRedirectToRoute('login');
});

test('an authorized user can view account listings page', function () {
    Server::factory()->has(Account::factory()->state([
        'domain' => 'mytestsite.com',
        'ip' => '255.1.1.100',
    ]))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::account.listings')
        ->assertCount('accounts', 1)
        ->assertSee('mytestsite.com');
});

test('the email accounts count is shown for each account', function () {
    $account = Account::factory()
        ->for(Server::factory())
        ->has(AccountEmail::factory()->count(3), 'emails')
        ->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::account.listings')
        ->assertSee('3');
});

test('an account with only the default system email shows none badge instead of one', function () {
    Account::factory()
        ->for(Server::factory())
        ->has(AccountEmail::factory()->count(1), 'emails')
        ->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::account.listings')
        ->assertSee('None');
});

test('accounts can be sorted by email count', function () {
    $server = Server::factory()->create();

    Account::factory()->for($server)->has(AccountEmail::factory()->count(3), 'emails')->create(['domain' => 'many-emails.com']);
    Account::factory()->for($server)->has(AccountEmail::factory()->count(1), 'emails')->create(['domain' => 'few-emails.com']);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::account.listings')
        ->call('sort', 'emails')
        ->assertSeeInOrder(['few-emails.com', 'many-emails.com']);
});

test('the account listings are in alphabetical order', function () {
    Server::factory()->has(Account::factory()->count(3)->state(new Sequence(
        ['domain' => 'somesite.com'],
        ['domain' => 'anothersite.com'],
        ['domain' => 'thelastsite.com'],
    )))->create();

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::account.listings')
        ->assertSeeInOrder(['anothersite.com', 'somesite.com', 'thelastsite.com']);
});
