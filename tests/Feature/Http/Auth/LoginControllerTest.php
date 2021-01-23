<?php

namespace Tests\Feature\Http\Auth;

use Arr;
use Hash;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanLoginViaApiRest()
    {
        $response = $this->makeTestLoginRequest();

        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token'
        ]);

        $response->assertStatus(200);
    }


    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanRefresTokenViaApiRestWithCookie()
    {
        $response = $this->makeTestLoginRequest();

        $cookie = $this->getCookie($response, "refresh-token", false);

        $cookies = ["refresh-token" => $cookie->getValue()];

        $refreshResponse = $this->call('POST', route('auth.refresh'), [], $cookies);

        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token'
        ]);

        $response->assertStatus(200);
    }

    protected function createOauthClient()
    {
        $clientName = config('services.passport.oauth.clients.webapp.name');

        return Client::factory()->create([
            "user_id" => null,
            "name" => $clientName,
            "provider" => "users",
            "redirect" => "http://localhost",
            "personal_access_client" => false,
            "password_client" => true,
            "revoked" => false,
        ]);
    }

    protected function createUser($email, $password)
    {
        return User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);
    }

    protected function makeTestLoginRequest()
    {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $this->createOauthClient();
        $this->createUser($email, $password);

        return $this->postJson(
            route('auth.login'),
            [
                'email' => $email,
                'password' => $password
            ]
        );
    }

    protected function getCookie($response, $cookieName)
    {
        $cookie = Arr::first(
            $response->headers->getCookies(),
            function ($cookie, $index) use ($cookieName) {
                return $cookie->getName() === $cookieName;
            }
        );

        return $cookie;
    }
}
