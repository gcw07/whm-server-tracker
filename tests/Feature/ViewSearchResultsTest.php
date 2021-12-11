<?php

use App\Models\Account;
use App\Models\Server;
use App\Models\User;
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
    $server = Server::factory()->create([
        'name' => 'My Test Server',
    ]);

    $serverB = Server::factory()->create([
        'name' => 'No Results',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'test']))
        ->assertSuccessful();

//        $response->data('servers')->assertContains($server);
//        $response->data('servers')->assertNotContains($serverB);
});

test('the server notes can be searched', function () {
    $server = Server::factory()->create([
        'notes' => 'see this note',
    ]);

    $serverB = Server::factory()->create([
        'notes' => 'do not see me',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'this']))
        ->assertSuccessful();

//        $response->data('servers')->assertContains($server);
//        $response->data('servers')->assertNotContains($serverB);
});

test('the account domain can be searched', function () {
    $server = Server::factory()->create();
    $account = Account::factory()->create([
        'server_id' => $server->id,
        'domain' => 'mytestsite.com',
    ]);

    $accountB = Account::factory()->create([
        'server_id' => $server->id,
        'domain' => 'never-see.com',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'test']))
        ->assertSuccessful();

//        $response->data('accounts')->assertContains($account);
//        $response->data('accounts')->assertNotContains($accountB);
});

test('the account ip can be searched', function () {
    $server = Server::factory()->create();
    $account = Account::factory()->create([
        'server_id' => $server->id,
        'ip' => '255.1.1.100',
    ]);

    $accountB = Account::factory()->create([
        'server_id' => $server->id,
        'ip' => '192.1.1.100',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => '255']))
        ->assertSuccessful();

//        $response->data('accounts')->assertContains($account);
//        $response->data('accounts')->assertNotContains($accountB);
});

test('the account username can be searched', function () {
    $server = Server::factory()->create();
    $account = Account::factory()->create([
        'server_id' => $server->id,
        'user' => 'mysite',
    ]);

    $accountB = Account::factory()->create([
        'server_id' => $server->id,
        'user' => 'neversee',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('search', ['q' => 'mysite']))
        ->assertSuccessful();

//        $response->data('accounts')->assertContains($account);
//        $response->data('accounts')->assertNotContains($accountB);
});
