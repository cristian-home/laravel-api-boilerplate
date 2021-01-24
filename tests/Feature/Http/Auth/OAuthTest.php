<?php

namespace Tests\Feature\Http\Auth;

use Hash;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanGetAccessToken()
    {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $client = Client::factory()->create([
            'user_id' => null,
            'name' => 'SIM Web App',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);

        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $this->postJson('/oauth/token', [
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
        ])->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token',
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanRefreshAccessToken()
    {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $client = Client::factory()->create([
            'user_id' => null,
            'name' => 'SIM Web App',
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => false,
            'password_client' => true,
            'revoked' => false,
        ]);

        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/oauth/token', [
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password',
            'username' => $email,
            'password' => $password,
        ]);

        $this->postJson('/oauth/token', [
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $response->json()['refresh_token'],
        ])->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token',
        ]);
    }
}
