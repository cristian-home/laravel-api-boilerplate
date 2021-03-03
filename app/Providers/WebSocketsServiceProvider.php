<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class WebSocketsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Gate para laravel websocket
        Gate::define('viewWebSocketsDashboard', function ($user) {
            return in_array($user->email, ['chome@cpe.gov.co']);
        });
    }
}
