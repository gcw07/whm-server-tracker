<?php

namespace Tests\Feature;

use App\Models\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function logging_in_with_valid_credentials()
    {
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
    }

    /** @test */
    public function logging_in_with_invalid_credentials()
    {
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
    }

    /** @test */
    public function logging_in_with_an_account_that_does_not_exist()
    {
        $response = $this->post(route('login'), [
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
    public function logging_out_the_current_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertFalse(Auth::check());
    }

    /** @test */
    public function logging_in_updates_last_login_at_and_last_ip_address()
    {
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
    }
}
