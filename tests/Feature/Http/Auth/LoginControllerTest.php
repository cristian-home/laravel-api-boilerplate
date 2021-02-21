<?php

namespace Tests\Feature\Http\Auth;

use Arr;
use Hash;
use Tests\TestCase;
use App\Models\User;
use Custom\OTP\OTPConstants;
use Google2FA;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Recovery\Recovery;

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
            'access_token',
        ]);

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserWith_2faEnabledCanLoginViaApiRestWithOtp()
    {
        $response = $this->makeTestLoginRequestWithOtp();

        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
        ]);

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserWith_2faEnabledCanNotLoginViaApiRestWithWrongOtp()
    {
        $response = $this->makeTestLoginRequestWithOtp(false, '123456');

        // $response->assertJsonStructure([
        //     'token_type',
        //     'expires_in',
        //     'access_token',
        // ]);

        $response->assertStatus(422);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserWith_2faEnabledCanLoginViaApiRestWithRecoveryCode()
    {
        $response = $this->makeTestLoginRequestWithOtp(false);

        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
        ]);

        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserWith_2faEnabledCanNotLoginViaApiRestWithWrongRecoveryCode()
    {
        $response = $this->makeTestLoginRequestWithOtp(
            true,
            null,
            'xeh1XUONsh-6cwWGW7Kam',
        );

        $response->assertStatus(422);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanRefresTokenViaApiRestWithCookie()
    {
        $response = $this->makeTestLoginRequest();

        $cookie = $this->getCookie($response, 'refresh-token', false);

        $cookies = ['refresh-token' => $cookie->getValue()];

        $refreshResponse = $this->call(
            'POST',
            route('auth.refresh'),
            [],
            $cookies,
        );

        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
        ]);

        $response->assertStatus(200);
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

    protected function makeTestLoginRequestWithOtp(
        $with_recovery_code = false,
        $otp = null,
        $recovery_code = null
    ) {
        $email = $this->faker->safeEmail;
        $password = $this->faker->password;

        $this->createOauthClient();
        $user = $this->createUser($email, $password, true);

        // Inicializar la clase 2FA
        $google2fa = app('pragmarx.google2fa');

        // Obtener el código OTP a partir del secret
        $otp =
            $otp ??
            $google2fa->getCurrentOtp($user->{OTPConstants::OTP_SECRET_COLUMN});

        $recovery_code =
            $recovery_code ??
            $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN}[0];

        $data = [
            'email' => $email,
            'password' => $password,
        ];

        $key = $with_recovery_code
            ? OTPConstants::RECOVERY_CODE_INPUT_FIELD
            : OTPConstants::OTP_INPUT_FIELD;

        $value = $with_recovery_code ? $recovery_code : $otp;

        $data = Arr::add($data, $key, $value);

        return $this->postJson(route('auth.login'), $data);
    }

    protected function createUser($email, $password, $enabled2fa = false)
    {
        return User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
            OTPConstants::OTP_ENABLED_COLUMN => $enabled2fa,
            OTPConstants::OTP_SECRET_COLUMN => $enabled2fa
                ? Google2FA::generateSecretKey()
                : null,
            OTPConstants::OTP_RECOVERY_CODES_COLUMN => $enabled2fa
                ? (new Recovery())->toArray()
                : null,
        ]);
    }

    protected function getCookie($response, $cookieName)
    {
        $cookie = Arr::first($response->headers->getCookies(), function (
            $cookie,
            $index
        ) use ($cookieName) {
            return $cookie->getName() === $cookieName;
        });

        return $cookie;
    }
}
