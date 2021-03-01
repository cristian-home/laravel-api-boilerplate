<?php

namespace Tests\Feature\Http\Api\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanRequestAPasswordReset()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.password.email'), [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testResetPasswordNotificationIsSentToUser()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->postJson(route('api.password.email'), [
            'email' => $user->email,
        ]);

        Notification::assertSentTo([$user], ResetPasswordNotification::class);
    }
}
