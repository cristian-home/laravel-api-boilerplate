<?php

namespace App\Http\Middleware;

use Closure;
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
        $except = ['password', 'password_confirmation'];

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
