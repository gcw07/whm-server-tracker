<?php

namespace Tests\Feature;

use Auth;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function logging_in_with_valid_credentials()
    {
        $user = create('App\User', [
            'email' => 'john@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'super-secret-password',
        ]);

        $response->assertRedirect('/dashboard');

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
    }

    /** @test */
    function logging_in_with_invalid_credentials()
    {
        $user = create('App\User', [
            'email' => 'john@example.com',
            'password' => bcrypt('super-secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'john@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/');

        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_in_with_an_account_that_does_not_exist()
    {
        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'not-the-right-password',
        ]);

        $response->assertRedirect('/');

        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_out_the_current_user()
    {
        $this->signIn();

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    function logging_in_updates_last_login_at_and_last_ip_address()
    {
        $user = create('App\User', [
            'email' => 'jane@example.com',
            'password' => bcrypt('secret-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'secret-password',
        ]);

        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->is($user));
        $this->assertNotNull(Auth::user()->last_login_at);
        $this->assertNotNull(Auth::user()->last_login_ip_address);
    }
}
