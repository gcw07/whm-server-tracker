<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /** @test */
    public function guests_cannot_change_a_password()
    {
        $this->putJson(route('users.change-password', $this->user->id), [
            'password'              => 'secret',
            'password_confirmation' => 'secret',
        ])->assertUnauthorized();
    }

    /** @test */
    public function an_authorized_user_can_change_a_password()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->putJson(route('users.change-password', $this->user->id), [
                'password'              => 'secret',
                'password_confirmation' => 'secret'
            ])->assertSuccessful();
    }

    /**
     * @dataProvider validationDataProvider
     * @test
     * @param $field
     * @param $value
     * @param $errorMessage
     */
    public function validate_rules_for_user_change_password($field, $value, $errorMessage)
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('users.change-password', $this->user->id), [
                $field => $value,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$field => $errorMessage]);
    }

    public function validationDataProvider()
    {
        return [
            'password is required' => ['password', '', 'field is required'],
            'password must be at least 6 characters' => ['password', Str::random(5), 'must be at least 6 characters'],
        ];
    }

    /** @test */
    public function password_confirmation_is_required_for_user_change_password()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('users.change-password', $this->user->id), [
                'password' => 'secret',
                'password_confirmation' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password' => 'confirmation does not match']);
    }
}
