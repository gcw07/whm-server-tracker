<?php

use App\Http\Livewire\Server\ResetToken as ServerResetToken;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->server = Server::factory()->create(['token' => 'valid-api-token']);
});

test('guests cannot access the reset server token component', function () {
    $response = Livewire::test(ServerResetToken::class, ['server' => $this->server]);
    $response->assertStatus(401);
});

test('an authorized user can access the reset server token component', function () {
    $this->actingAs(User::factory()->create());

    $response = Livewire::test(ServerResetToken::class, ['server' => $this->server]);
    $response->assertSuccessful();
});
