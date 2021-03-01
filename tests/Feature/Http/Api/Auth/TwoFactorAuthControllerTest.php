<?php

namespace Tests\Feature\Http\Api\Auth;

use Google2FA;
use Tests\TestCase;
use App\Models\User;
use Custom\OTP\OTPConstants;
use PragmaRX\Recovery\Recovery;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TwoFactorAuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanRequestEnableTwoFactorAuthentication()
    {
        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->getJson(route('api.auth.2fa.enablerequest'));

        $response->assertStatus(200);

        $response->assertJsonStructure(['otp_secret', 'expire', 'qr_svg']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanNotRequestEnableTwoFactorAuthenticationIfAlreadyEnabled()
    {
        $this->actingAs(
            User::factory()->create([
                OTPConstants::OTP_ENABLED_COLUMN => true,
                OTPConstants::OTP_SECRET_COLUMN => Google2FA::generateSecretKey(),
                OTPConstants::OTP_RECOVERY_CODES_COLUMN => (new Recovery())->toArray(),
            ]),
            'api',
        );

        $response = $this->getJson(route('api.auth.2fa.enablerequest'));

        $response->assertStatus(422);

        // $response->assertJsonStructure(['otp_secret', 'expire', 'qr_svg']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testUserCanEnableTwoFactorAuthentication()
    {
        $this->actingAs(User::factory()->create(), 'api');

        // Gnerar secret temporal
        $requestResponse = $this->getJson(route('api.auth.2fa.enablerequest'));

        // Inicializar la clase 2FA
        $google2fa = app('pragmarx.google2fa');

        // Obtener el código OTP a partir del secret
        $otp = $google2fa->getCurrentOtp($requestResponse['otp_secret']);

        // Hacer petición a endpoint para habilitar 2FA
        $response = $this->postJson(route('api.auth.2fa.enable'), [
            OTPConstants::OTP_INPUT_FIELD => $otp,
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message', 'recovery_codes']);
    }
}
