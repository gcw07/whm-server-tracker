<?php

use App\Jobs\FetchServerDetailsJob;
use App\Models\Server;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(LazilyRefreshDatabase::class);

it('dispatches fetch jobs only for servers with tokens', function () {
    Queue::fake();

    $serverWithToken = Server::factory()->create(['token' => 'valid-token']);
    $serverWithoutToken = Server::factory()->create(['token' => null]);

    $this->artisan('server-tracker:fetch-server-details')->assertSuccessful();

    Queue::assertPushedOn('high', FetchServerDetailsJob::class, function ($job) use ($serverWithToken) {
        return $job->server->is($serverWithToken);
    });

    Queue::assertNotPushed(FetchServerDetailsJob::class, function ($job) use ($serverWithoutToken) {
        return $job->server->is($serverWithoutToken);
    });
});

it('does not dispatch fetch jobs for servers without tokens', function () {
    Queue::fake();

    Server::factory()->create(['token' => null]);

    $this->artisan('server-tracker:fetch-server-details')->assertSuccessful();

    Queue::assertNothingPushed();
});
