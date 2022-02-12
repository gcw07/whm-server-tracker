<?php

use App\Enums\ServerTypeEnum;
use App\Models\Account;
use App\Models\Server;
use App\Models\User;
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
    $this->actingAs($this->user)
        ->get(route('accounts.index'))
        ->assertSuccessful();
});

test('guests can not view account api listings', function () {
    $this->get(route('accounts.listing'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view account api listings', function () {
    $server = Server::factory()->create();
    Account::factory()->create([
        'server_id' => $server->id,
        'domain' => 'mytestsite.com',
        'ip' => '255.1.1.100',
    ]);

    $response = $this->actingAs($this->user)
        ->get(route('accounts.listing'))
        ->assertSuccessful();

    tap($response->json(), function ($accounts) {
        $this->assertCount(1, $accounts);
        $this->assertEquals('mytestsite.com', $accounts[0]['domain']);
        $this->assertEquals('255.1.1.100', $accounts[0]['ip']);
    });
});

test('the account listings are in alphabetical order', function () {
    $server = Server::factory()->create();

    $accountA = Account::factory()->create(['server_id' => $server->id, 'domain' => 'somesite.com']);
    $accountB = Account::factory()->create(['server_id' => $server->id, 'domain' => 'anothersite.com']);
    $accountC = Account::factory()->create(['server_id' => $server->id, 'domain' => 'thelastsite.com']);

    $response = $this->actingAs($this->user)
        ->get(route('accounts.listing'))
        ->assertSuccessful();

    $response->jsonData()->assertEquals([
        $accountB,
        $accountA,
        $accountC,
    ]);
});

test('the account listings can be filtered by server', function () {
    $serverA = Server::factory()->create(['server_type' => ServerTypeEnum::vps()]);
    $serverB = Server::factory()->create(['server_type' => ServerTypeEnum::dedicated()]);

    $accountA = Account::factory()->create(['server_id' => $serverA->id, 'domain' => 'somedomain.com']);
    $accountA = Account::factory()->create(['server_id' => $serverB->id, 'domain' => 'anotherdomain.com']);

    $response = $this->actingAs($this->user)
        ->get(route('accounts.server-listing', $serverA->id))
        ->assertSuccessful();

    tap($response->json(), function ($accounts) {
        $this->assertCount(1, $accounts);
        $this->assertEquals('somedomain.com', $accounts[0]['domain']);
    });
});
