<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class LaravelWebSocketsAuthorize
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
        return app()->environment('local') ||
            Gate::check('viewWebSocketsDashboard', [$request->user()])
            ? $next($request)
            : abort(403);
    }
}
