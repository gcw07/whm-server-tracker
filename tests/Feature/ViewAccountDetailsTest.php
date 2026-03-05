<?php

use App\Models\Account;
use App\Models\AccountEmail;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests can not view account details page', function () {
    $account = Account::factory()->for(Server::factory())->create();

    $this->get(route('accounts.show', $account->id))
        ->assertRedirectToRoute('login');
});

test('an authorized user can view account details page', function () {
    $account = Account::factory()->for(Server::factory())->create(['domain' => 'testdomain.com']);

    $this->actingAs($this->user);

    Livewire::test('pages::account.details', ['account' => $account])
        ->assertSee('testdomain.com');
});

test('the email accounts table renders email addresses', function () {
    $account = Account::factory()->for(Server::factory())->create();

    AccountEmail::factory()->create([
        'account_id' => $account->id,
        'email' => 'hello@example.com',
        'disk_used' => 2_621_440,
        'disk_quota' => 262_144_000,
        'disk_used_percent' => 1.0,
        'suspended_incoming' => false,
        'suspended_login' => false,
    ]);

    $this->actingAs($this->user);

    Livewire::test('pages::account.details', ['account' => $account])
        ->assertSee('hello@example.com')
        ->assertSee('2.5 MB')
        ->assertSee('250 MB')
        ->assertSee('1.0%')
        ->assertSee('Active');
});

test('emails are sorted by disk used descending', function () {
    $account = Account::factory()->for(Server::factory())->create();

    AccountEmail::factory()->create([
        'account_id' => $account->id,
        'email' => 'small@example.com',
        'disk_used' => 1_000,
    ]);

    AccountEmail::factory()->create([
        'account_id' => $account->id,
        'email' => 'large@example.com',
        'disk_used' => 100_000_000,
    ]);

    $this->actingAs($this->user);

    Livewire::test('pages::account.details', ['account' => $account])
        ->assertSeeInOrder(['large@example.com', 'small@example.com']);
});

test('shows unlimited for emails with no quota', function () {
    $account = Account::factory()->for(Server::factory())->create();

    AccountEmail::factory()->create([
        'account_id' => $account->id,
        'disk_quota' => 0,
    ]);

    $this->actingAs($this->user);

    Livewire::test('pages::account.details', ['account' => $account])
        ->assertSee('Unlimited');
});

test('shows no email accounts message when account has no emails', function () {
    $account = Account::factory()->for(Server::factory())->create();

    $this->actingAs($this->user);

    Livewire::test('pages::account.details', ['account' => $account])
        ->assertSee('No email accounts found.');
});

test('shows suspension badges for suspended emails', function () {
    $account = Account::factory()->for(Server::factory())->create();

    AccountEmail::factory()->create([
        'account_id' => $account->id,
        'suspended_incoming' => true,
        'suspended_login' => true,
    ]);

    $this->actingAs($this->user);

    Livewire::test('pages::account.details', ['account' => $account])
        ->assertSee('Incoming')
        ->assertSee('Login');
});
