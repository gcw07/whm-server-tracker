<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_password_should_be_encrypted()
    {
        $user = make('App\User', [
            'name'     => 'John Doe',
            'password' => 'secret'
        ]);

        $this->assertNotEquals('secret', $user->password);
    }

    /** @test */
    public function the_password_should_not_be_included_in_returned_data()
    {
        $user = make('App\User', [
            'name'     => 'John Doe',
            'password' => 'secret'
        ]);

        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    /** @test */
    public function the_remember_token_should_not_be_included_in_returned_data()
    {
        $user = make('App\User', [
            'name'           => 'John Doe',
            'remember_token' => 'some-token'
        ]);

        $this->assertArrayNotHasKey('remember_token', $user->toArray());
    }
}
