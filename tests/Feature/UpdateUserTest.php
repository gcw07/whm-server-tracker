<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    private function validParams($overrides = [])
    {
        return array_merge([
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ], $overrides);
    }

    /** @test */
    public function guests_cannot_view_the_edit_user_form()
    {
        $this->get(route('users.edit', $this->user->id))
            ->assertRedirect(route('login'));
    }

    /** @test */
    public function an_authorized_user_can_view_the_edit_user_form()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.edit', $this->user->id))
            ->assertSuccessful();
    }

    /** @test */
    public function guests_cannot_edit_a_user()
    {
        $user = User::factory()->create(['name' => 'Grant Williams']);

        $this->putJson(route('users.update', $user->id), $this->validParams())
            ->assertUnauthorized();

        tap($user->fresh(), function (User $user) {
            $this->assertEquals('Grant Williams', $user->name);
        });
    }

    /** @test */
    public function an_authorized_user_can_edit_a_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('users.update', $this->user->id), $this->validParams([
                'name' => 'Della Duck',
                'email' => 'della@example.com',
            ]));

        $response->assertRedirect(route('users.index'));

        tap($this->user->fresh(), function (User $user) {
            $this->assertEquals('Della Duck', $user->name);
            $this->assertEquals('della@example.com', $user->email);
        });
    }

    /**
     * @dataProvider validationDataProvider
     * @test
     * @param $field
     * @param $value
     * @param $errorMessage
     */
    public function validate_rules_for_user_edit($field, $value, $errorMessage)
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->putJson(route('users.update', $this->user->id), $this->validParams([
                $field => $value,
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$field => $errorMessage]);
    }

    public function validationDataProvider()
    {
        return [
            'name is required' => ['name', '', 'field is required'],
            'email is required' => ['email', '', 'field is required'],
            'email must be valid' => ['email', 'not-valid-email', 'must be a valid email address'],
        ];
    }

    /** @test */
    public function email_must_be_unique_for_user_edit()
    {
        $user = User::factory()->create(['email' => 'grant@example.com']);

        $response = $this->actingAs($user)
            ->putJson(route('users.update', $this->user->id), $this->validParams([
                'email' => 'grant@example.com',
            ]));

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email' => 'has already been taken']);
    }

    /** @test */
    public function email_can_be_the_same_for_the_same_user_for_user_edit()
    {
        $user = User::factory()->create(['email' => 'grant@example.com']);
        $userB = User::factory()->create(['email' => 'mike@example.com']);

        $response = $this->actingAs($user)
            ->putJson(route('users.update', $userB->id), $this->validParams([
                'email' => 'mike@example.com',
            ]));

        $response->assertRedirect(route('users.index'));

        tap($userB->fresh(), function (User $user) {
            $this->assertEquals('mike@example.com', $user->email);
        });
    }
}
