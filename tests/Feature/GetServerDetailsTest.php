<?php

use App\Connectors\FakeServerConnector;
use App\Connectors\ServerConnector;
use App\Jobs\FetchServerDetails;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests cannot fetch server details', function () {
    $server = Server::factory()->create();

    $this->get(route('servers.fetch-details', $server->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can fetch server details', function () {
    $user = User::factory()->create();
    $server = Server::factory()->create([
        'name' => 'my-server-name',
        'address' => '1.1.1.1',
        'port' => 1000,
        'server_type' => 'vps',
        'token' => 'valid-server-api-token',
    ]);

    Queue::fake();

    $fake = new FakeServerConnector;
    $this->app->instance(ServerConnector::class, $fake);

    $this->actingAs($user)
        ->get(route('servers.fetch-details', $server->id))
        ->assertSuccessful();

    Queue::assertPushed(FetchServerDetails::class, function ($job) use ($server) {
        return $job->server->is($server);
    });
});
