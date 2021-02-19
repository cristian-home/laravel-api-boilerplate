<?php

namespace App\Providers;

use App\Events\NewUser;
use Illuminate\Auth\Events\Registered;
use App\Events\TwoFactorAuthEnabledEvent;
use App\Events\TwoFactorAuthDisabledEvent;
use App\Listeners\SendNewUserNotification;
use App\Listeners\SendTwoFactorAuthEnabledNotification;
use App\Listeners\SendTwoFactorAuthDisabledNotification;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        NewUser::class => [SendNewUserNotification::class],
        Registered::class => [SendEmailVerificationNotification::class],
        TwoFactorAuthEnabledEvent::class => [
            SendTwoFactorAuthEnabledNotification::class,
        ],
        TwoFactorAuthDisabledEvent::class => [
            SendTwoFactorAuthDisabledNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
