<?php

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use App\Jobs\FetchServerAccounts;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests cannot fetch server accounts', function () {
    $server = Server::factory()->create();

    $this->get(route('servers.fetch-accounts', $server->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can fetch server accounts', function () {
    $user = User::factory()->create();
    $server = Server::factory()->create([
        'name' => 'my-server-name',
        'address' => '1.1.1.1',
        'port' => 2087,
        'server_type' => 'vps',
        'token' => 'valid-api-token',
    ]);

    Queue::fake();

    $fake = new FakeServerConnector;
    $this->app->instance(ServerConnector::class, $fake);

    $this->actingAs($user)
        ->get(route('servers.fetch-accounts', $server->id))
        ->assertSuccessful();

    Queue::assertPushed(FetchServerAccounts::class, function ($job) use ($server) {
        return $job->server->is($server);
    });
});
