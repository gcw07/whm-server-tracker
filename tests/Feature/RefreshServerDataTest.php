<?php

use App\Jobs\FetchServerDataJob;
use App\Models\Server;
use App\Models\User;
use App\Services\WHM\WhmApi;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\WhmApiFake;

uses(LazilyRefreshDatabase::class);

test('guests cannot refresh server data', function () {
    $server = Server::factory()->create();

    $this->get(route('servers.refresh', $server->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can refresh server data', function () {
    $user = User::factory()->create();
    $server = Server::factory()->create([
        'name' => 'my-server-name',
        'address' => '1.1.1.1',
        'port' => 1000,
        'server_type' => 'vps',
        'token' => 'valid-server-api-token',
    ]);

    Queue::fake();

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    $this->actingAs($user)
        ->get(route('servers.refresh', $server->id))
        ->assertSuccessful();

    Queue::assertPushed(FetchServerDataJob::class, function ($job) use ($server) {
        return $job->server->is($server);
    });
});
