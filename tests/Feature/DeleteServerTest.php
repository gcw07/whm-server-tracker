<?php

use App\Livewire\Server\Delete as ServerDelete;
use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Spatie\UptimeMonitor\Models\Monitor;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->server = Server::factory()->create();
});

test('guests cannot access the delete server component', function () {
    $response = Livewire::test(ServerDelete::class, ['server' => $this->server]);
    $response->assertStatus(401);
});

test('an authorized user can delete a server', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ServerDelete::class, ['server' => $this->server])
        ->call('delete')
        ->assertRedirectToRoute('servers.index');

    $this->assertEquals(0, Server::count());
});

test('accounts are deleted when a server is deleted', function () {
    $server = Server::factory()
        ->has(Account::factory()->count(5))
        ->create(['name' => 'my-server-name']);

    Server::factory()
        ->has(Account::factory()->count(1))
        ->create(['name' => 'other-server-name']);

    $this->assertEquals(3, Server::count()); // +1 from the beforeEach

    tap($server->fresh(), function (Server $server) {
        $this->assertCount(5, $server->accounts);
    });

    $this->actingAs(User::factory()->create());

    Livewire::test(ServerDelete::class, ['server' => $server])
        ->call('delete')
        ->assertRedirectToRoute('servers.index');

    $this->assertEquals(2, Server::count());
    $this->assertEquals(1, Account::count());
});

test('monitors are deleted when a server is deleted', function () {
    $server = Server::factory()
        ->has(Account::factory()->count(5))
        ->create(['name' => 'my-server-name']);

    foreach ($server->accounts as $account) {
        Monitor::create([
            'url' => $account->domain_url,
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => true,
        ]);
    }

    $otherServer = Server::factory()
        ->has(Account::factory()->count(1))
        ->create(['name' => 'other-server-name']);

    foreach ($otherServer->accounts as $account) {
        Monitor::create([
            'url' => $account->domain_url,
            'uptime_check_enabled' => true,
            'certificate_check_enabled' => true,
        ]);
    }

    $this->assertEquals(6, Monitor::count());

    $this->actingAs(User::factory()->create());

    Livewire::test(ServerDelete::class, ['server' => $server])
        ->call('delete')
        ->assertRedirectToRoute('servers.index');

    $this->assertEquals(2, Server::count());
    $this->assertEquals(1, Account::count());
    $this->assertEquals(1, Monitor::count());
});
