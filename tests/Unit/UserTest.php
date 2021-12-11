<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('email address should be lowercase', function () {
    $user = User::factory()->create([
        'email' => 'JOHN@example.COM'
    ]);

    $this->assertEquals('john@example.com', $user->email);
});
