<?php

namespace App\Http\Controllers\Api\Auth;

use Google2FA;
use App\Models\User;
use App\Rules\ValidOTP;
use Illuminate\Http\Request;
use Custom\OTP\OTPConstants;
use PragmaRX\Recovery\Recovery;
use App\Http\Controllers\Controller;
use App\Events\TwoFactorAuthEnabledEvent;
use App\Events\TwoFactorAuthDisabledEvent;

class TwoFactorAuthController extends Controller
{
    /**
     * Constructor de la nueva instancia del controlador
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:api', 'verified']);
        $this->middleware(['2fa.status:disabled'])->only([
            'requestEnable2FA',
            'enable2FA',
        ]);
        $this->middleware(['2fa.api', '2fa.status:enabled'])->only([
            'disable2FA',
        ]);
        $this->middleware('2fa.status:enabled')->only('getInlineQRCode');
    }

    /**
     * Habilitar 2FA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function requestEnable2FA(Request $request)
    {
        $user = $request->user();

        // generate a new temp secret key for the user
        $user->temp_secret = Google2FA::generateSecretKey();

        $inlineQRCode = $this->getInlineSvgQRCode($user, $temp = true);

        return response()->json([
            'otp_secret' => $user->temp_secret,
            'expire' => now()
                ->addMinutes(10)
                ->toDateTimeString(),
            'qr_svg' => $inlineQRCode,
        ]);
    }

    /**
     * Habilitar 2FA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enable2FA(Request $request)
    {
        $user = $request->user();

        // Verificar que el temp_secret no haya expirado
        if ($user->tempSecretExpired()) {
            return response()->json(
                [
                    'message' => __(
                        'The request to enable two-factor authentication has expired.',
                    ),
                ],
                422,
            );
        }

        // renovar tiempo de expiración de temp secret en cache
        $user->renewTempSecret();

        // Validar código OTP
        $request->validate(
            [
                'one_time_password' => [
                    'required',
                    new ValidOTP($user->temp_secret),
                ],
            ],
            [
                'one_time_password.required' => _(
                    config('google2fa.error_messages.cannot_be_empty'),
                ),
            ],
        );

        $user->revokeTempSecret();

        $recovery = new Recovery();

        $user->{OTPConstants::OTP_ENABLED_COLUMN} = true;
        $user->{OTPConstants::OTP_SECRET_COLUMN} = $user->temp_secret;
        $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN} = $recovery->toArray();
        $user->save();

        TwoFactorAuthEnabledEvent::dispatch($user);

        return response()->json([
            'message' => 'Autenticación de dos pasos habilitada',
            'recovery_codes' =>
                $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN},
        ]);
    }

    /**
     * Deshabilitar 2FA.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function disable2FA(Request $request)
    {
        $user = $request->user();

        $user->{OTPConstants::OTP_ENABLED_COLUMN} = false;
        $user->{OTPConstants::OTP_SECRET_COLUMN} = null;

        $user->save();

        TwoFactorAuthDisabledEvent::dispatch($user);

        return response()->json(null, 204);
    }

    /**
     * Generar QR SVG.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getInlineQRCode(Request $request)
    {
        $user = $request->user();

        $inlineQRCode = $this->getInlineSvgQRCode($user);

        return $inlineQRCode;
    }

    /**
     * Generar QR SVG.
     *
     * @param  \App\Models\User $user
     * @return string
     */
    public function getInlineSvgQRCode(User $user, $temp = false)
    {
        $inlineQRCode = Google2FA::getQRCodeInline(
            config('app.name'),
            $user->email,
            $temp
                ? $user->temp_secret
                : $user->{OTPConstants::OTP_SECRET_COLUMN},
        );

        return $inlineQRCode;
    }
}
