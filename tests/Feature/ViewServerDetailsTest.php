<?php

use App\Jobs\FetchServerDataJob;
use App\Livewire\Server\Details as ServerDetails;
use App\Models\Server;
use App\Models\User;
use App\Services\WHM\WhmApi;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\WhmApiFake;

uses(LazilyRefreshDatabase::class);

test('guests can not view server details page', function () {
    $server = Server::factory()->create();

    $this->get(route('servers.show', $server->id))
        ->assertRedirectToRoute('login');
});

test('an authorized user can view server details page', function () {
    $server = Server::factory()->create(['name' => 'MyServer.com']);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::server.details', ['server' => $server])
        ->assertSee('MyServer.com');
});

test('an authorized user can refresh server data from server details', function () {
    $server = Server::factory()->create([
        'name' => 'MyServer.com',
        'address' => '1.1.1.1',
        'port' => 1000,
        'server_type' => 'vps',
        'token' => 'valid-server-api-token',
    ]);

    Queue::fake();

    $fake = new WhmApiFake;
    $this->app->instance(WhmApi::class, $fake);

    $this->actingAs(User::factory()->create());

    Livewire::test('pages::server.details', ['server' => $server])
        ->call('refresh');

    Queue::assertPushed(FetchServerDataJob::class, function ($job) use ($server) {
        return $job->server->is($server);
    });
});
