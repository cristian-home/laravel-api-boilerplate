<?php

namespace Tests\Feature\Http\Auth;

use App\Models\User;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Tests\TestCase;

class LoggedUserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testAuthenticatedUserCheckAuth()
    {
        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->getJson(route('auth.check'));

        $response->assertSuccessful();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUnauthenticatedUserCheckAuth()
    {
        $response = $this->getJson(route('auth.check'));

        $response->assertUnauthorized();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNonVerifiedAuthenticatedUserCheckAuth()
    {
        $this->actingAs(
            User::factory()->create([
                'email_verified_at' => null,
            ]),
            'api',
        );

        $response = $this->getJson(route('auth.check'));

        $response->assertForbidden();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanGetCurrentUserInfo()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        $response = $this->getJson(route('auth.user'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => ['id', 'email', 'email_verified_at'],
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testLoggedUserCanLogOut()
    {
        $accessToken = $this->makeTestLoginRequest()
            ->assertSuccessful()
            ->json()['access_token'];

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])
            ->getJson(route('auth.check'))
            ->assertSuccessful();

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])
            ->postJson(route('auth.logout'))
            ->assertNoContent()
            ->assertSuccessful();
    }

    protected function createOauthClient()
    {
        $clientName = config('services.passport.oauth.clients.webapp.name');

        return Client::factory()->create([
            'user_id' => null,
            'name' => $clientName,
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);
    }

    protected function createUser($email, $password)
    {
        return User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }

    protected function makeTestLoginRequest()
    {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $this->createOauthClient();
        $this->createUser($email, $password);

        return $this->postJson(route('auth.login'), [
            'email' => $email,
            'password' => $password,
        ]);
    }
}
