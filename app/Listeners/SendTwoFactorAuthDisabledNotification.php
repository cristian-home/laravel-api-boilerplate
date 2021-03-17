<?php

namespace App\Listeners;

use App\Notifications\TwoFactorAuthDisabledNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Notification;

class SendTwoFactorAuthDisabledNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        Notification::send(
            $event->user,
            new TwoFactorAuthDisabledNotification($event->user),
        );
    }
}
