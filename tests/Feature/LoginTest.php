<?php

use App\Models\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(LazilyRefreshDatabase::class);

test('logging in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('super-secret-password'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'john@example.com',
        'password' => 'super-secret-password',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertTrue(Auth::check());
    $this->assertTrue(Auth::user()->is($user));
});

test('logging in with invalid credentials', function () {
    User::factory()->create([
        'email' => 'john@example.com',
        'password' => bcrypt('super-secret-password'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'john@example.com',
        'password' => 'not-the-right-password',
    ]);

    $response->assertRedirect('/');

    $response->assertSessionHasErrors('email');
    $this->assertTrue(session()->hasOldInput('email'));
    $this->assertFalse(session()->hasOldInput('password'));
    $this->assertFalse(Auth::check());
});

test('logging in with an account that does not exist', function () {
    $response = $this->post(route('login'), [
        'email' => 'nobody@example.com',
        'password' => 'not-the-right-password',
    ]);

    $response->assertRedirect('/');

    $response->assertSessionHasErrors('email');
    $this->assertTrue(session()->hasOldInput('email'));
    $this->assertFalse(session()->hasOldInput('password'));
    $this->assertFalse(Auth::check());
});

test('logging out the current user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/logout');

    $response->assertRedirect('/');
    $this->assertFalse(Auth::check());
});

test('logging in updates last login at and last ip address', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => bcrypt('secret-password'),
    ]);

    $response = $this->post(route('login'), [
        'email' => 'jane@example.com',
        'password' => 'secret-password',
    ]);

    $response->assertRedirect(route('dashboard'));

    $this->assertTrue(Auth::check());
    $this->assertTrue(Auth::user()->is($user));

    tap(Login::first(), function ($login) use ($user) {
        $this->assertEquals($user->id, $login->user_id);
        $this->assertNotNull($login->ip_address);
        $this->assertNotNull($login->created_at);
    });
});
