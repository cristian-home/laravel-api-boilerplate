<?php

namespace Tests\Feature\Http\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

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

        $response = $this->postJson(route('auth.register'), [
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

        $this->postJson(route('auth.register'), [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ]);

        $user = User::where('email', $email)->first();

        Notification::assertSentTo([$user], VerifyEmailNotification::class);
    }
}
