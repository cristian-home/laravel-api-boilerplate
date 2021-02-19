<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmailMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($verificationURL)
    {
        $this->verificationURL = $verificationURL;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $params = http_build_query(['signedurl' => $this->verificationURL]);
        $pwaURL = route('pwa.verification.verify') . '?' . $params;

        return $this->markdown('emails.users.verify')
            ->subject(__('Verify Email Address'))
            ->with([
                'verificationURL' => $this->verificationURL,
                'pwaURL' => $pwaURL,
            ]);
    }
}
