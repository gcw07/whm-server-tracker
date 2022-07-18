<?php

use App\Enums\ServerTypeEnum;
use App\Models\Account;
use App\Models\Server;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

test('guests can not view dashboard page', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('an authorized user can view dashboard page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful();
});
