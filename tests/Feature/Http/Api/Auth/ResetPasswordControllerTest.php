<?php

namespace Tests\Feature\Http\Api\Auth;

use Hash;
use Password;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanResetHisPassword()
    {
        $user = User::factory()->create([
            'password' => Hash::make('Password'),
        ]);

        $token = Password::getRepository()->create($user);

        $newPassword = $this->faker->password;

        $response = $this->postJson(route('api.password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertSuccessful();
    }

    use RefreshDatabase, WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCannotResetHisPasswordWithAnInvalidToken()
    {
        $user1 = User::factory()->create([
            'password' => Hash::make('Password'),
        ]);

        $user2 = User::factory()->create([
            'password' => Hash::make('Password'),
        ]);

        $token = Password::getRepository()->create($user2);

        $newPassword = $this->faker->password;

        $response = $this->postJson(route('api.password.update'), [
            'token' => $token,
            'email' => $user1->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);

        $response->assertStatus(422);
    }
}
