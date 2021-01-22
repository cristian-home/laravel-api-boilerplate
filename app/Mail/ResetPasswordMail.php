<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Lang;

class ResetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token, $emailAddress)
    {
        $this->token = $token;
        $this->emailAddress = $emailAddress;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $params = http_build_query([
            "token" => $this->token,
            "email" => $this->emailAddress
        ]);

        $pwaURL = url(route('pwa.password.reset')) . "?" . $params;

        return $this->markdown('emails.users.reset')
            ->subject(Lang::get('Reset Password Notification'))
            ->with([
                "actionUrl" => $pwaURL,
                "token" => $this->token
            ]);
    }
}
