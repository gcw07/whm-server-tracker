<?php

use App\Enums\ServerTypeEnum;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\Factories\ServerRequestDataFactory;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->server = Server::factory()->create(['name' => 'old-my-server-name']);
    $this->requestData = ServerRequestDataFactory::new();
});

test('guests cannot view the edit server form', function () {
    $this->get(route('servers.edit', $this->server->id))
        ->assertRedirect(route('login'));
});

test('an authorized user can view the edit server form', function () {
    $this->actingAs($this->user)
        ->get(route('servers.edit', $this->server->id))
        ->assertSuccessful();
});

test('guests cannot edit a server', function () {
    $this->putJson(route('servers.update', $this->server->id), $this->requestData->create())
        ->assertUnauthorized();

    tap($this->server->fresh(), function (Server $server) {
        $this->assertEquals('old-my-server-name', $server->name);
    });
});

test('an authorized user can edit a server', function () {
    $response = $this->actingAs($this->user)
        ->putJson(route('servers.update', $this->server->id), $this->requestData->create([
            'name' => 'My Server',
        ]));

    $response->assertRedirect(route('servers.index'));

    tap($this->server->fresh(), function (Server $server) {
        $this->assertEquals('My Server', $server->name);
    });
});

//test('the api token disk and backup details are cleared when reseller server type is selected', function () {
//    signIn();
//
//    $server = Server::factory()->create([
//        'server_type' => ServerTypeEnum::dedicated(),
//        'token' => 'old-api-token',
//    ]);
//
//    $server->settings()->merge([
//        'disk_used' => 10000000,
//        'disk_available' => 115000000,
//        'disk_total' => 125000000,
//        'disk_percentage' => 8,
//        'backup_enabled' => false,
//        'backup_days' => '1,2',
//        'backup_retention' => 10
//    ]);
//
//    $response = $this->putJson("/servers/{$server->id}", $this->requestData->create([
//        'server_type' => 'reseller'
//    ]));
//
//    tap($server->fresh(), function ($server) {
//        $this->assertEquals('reseller', $server->server_type);
//        $this->assertNull($server->token);
//        $this->assertNull($server->settings()->disk_used);
//        $this->assertNull($server->settings()->disk_available);
//        $this->assertNull($server->settings()->disk_total);
//        $this->assertNull($server->settings()->disk_percentage);
//        $this->assertNull($server->settings()->backup_enabled);
//        $this->assertNull($server->settings()->backup_days);
//        $this->assertNull($server->settings()->backup_retention);
//    });
//});

it('validates rules for server edit form', function ($data) {
    // This could be fixed in future pest version.
    $field = $data[0];
    $value = $data[1];
    $expectedResultType = $data[2];
    $errorMessage = $data[3];

    $response = $this->actingAs($this->user)
        ->putJson(route('servers.update', $this->server->id), $this->requestData->create([
            $field => $value,
        ]));

    if ($expectedResultType === 'invalid') {
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$field => $errorMessage]);
    } else {
        tap(Server::first(), function (Server $server) use ($field) {
            $this->assertNull($server->{$field});
        });
    }
})->with([
    fn() => ['name', '', 'invalid', 'field is required'],
    fn() => ['address', '', 'invalid', 'field is required'],
    fn() => ['port', '', 'invalid', 'field is required'],
    fn() => ['port', 'not-a-number', 'invalid', 'must be a number'],
    fn() => ['server_type', '', 'invalid', 'field is required'],
    fn() => ['server_type', 'not-valid-type', 'invalid', 'field is not a valid'],
    fn() => ['notes', '', 'success', null],
]);
