<?php

namespace Tests\Feature\Http\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanRegisterViaApiRest()
    {
        Notification::fake();

        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $response = $this->postJson(route('api.auth.register'), [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', $email)->first();

        Notification::assertSentTo([$user], VerifyEmailNotification::class);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testVerifyEmailNotificationIsSentToUser()
    {
        Notification::fake();

        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $this->postJson(route('api.auth.register'), [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', $email)->first();

        Notification::assertSentTo([$user], VerifyEmailNotification::class);
    }
}
