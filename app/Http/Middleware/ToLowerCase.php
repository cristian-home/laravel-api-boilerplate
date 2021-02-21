<?php

namespace App\Http\Middleware;

use Closure;
use Custom\OTP\OTPConstants;
use Illuminate\Http\Request;

class ToLowerCase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $except = [
            'password',
            'password_confirmation',
            OTPConstants::OTP_INPUT_FIELD,
            OTPConstants::RECOVERY_CODE_INPUT_FIELD,
        ];

        foreach ($request->all() as $key => $value) {
            if (is_string($value)) {
                $request[$key] = in_array($key, $except, true)
                    ? $value
                    : strtolower($value);
            }
        }

        return $next($request);
    }
}
