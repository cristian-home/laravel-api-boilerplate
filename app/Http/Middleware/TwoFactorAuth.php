<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Custom\OTP\OTPConstants;
use App\Services\TwoFactorAuthenticator;
use Exception;

class TwoFactorAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $parameter = null)
    {
        $authenticator = app(TwoFactorAuthenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        // if (
        //     $request->has(OTPConstants::RECOVERY_CODE_INPUT_FIELD) &&
        //     !$request->filled(OTPConstants::OTP_INPUT_FIELD)
        // ) {
        //     return $authenticator->makeFailedLoginRecoveryCodeResponse();
        // }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}
