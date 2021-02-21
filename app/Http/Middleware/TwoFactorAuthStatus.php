<?php

namespace App\Http\Middleware;

use Closure;
use Custom\OTP\OTPConstants;
use Illuminate\Http\Request;

class TwoFactorAuthStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $parameter = '')
    {
        $response = null;

        switch ($parameter) {
            case 'enabled':
                $response = $this->checkUser2FAStatus($request, true);
                break;
            case 'disabled':
                $response = $this->checkUser2FAStatus($request, false);
                break;
            default:
                abort(500, __('Invalid status in middleware'));
                break;
        }

        return $response ?? $next($request);
    }

    public function checkUser2FAStatus(Request $request, bool $condition)
    {
        $errormsg = $condition
            ? __('The user does not have two-factor authentication enabled.')
            : __('The user already has two-factor authentication enabled.');

        $status = (bool) $request->user()->{OTPConstants::OTP_ENABLED_COLUMN};

        if ($status !== $condition) {
            return $request->expectsJson()
                ? response()->json(['message' => $errormsg], 422)
                : redirect()
                    ->back()
                    ->withErrors(['message', $errormsg]);
        }

        return null;
    }
}
