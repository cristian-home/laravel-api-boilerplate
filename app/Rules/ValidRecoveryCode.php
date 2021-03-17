<?php

namespace App\Rules;

use App\Services\TwoFactorAuthenticator;
use Illuminate\Contracts\Validation\Rule;

class ValidRecoveryCode implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $authenticator = app(TwoFactorAuthenticator::class)->bootStateless(
            request(),
        );

        return $authenticator->checkValidRecoveryCode($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __(config('google2fa.error_messages.wrong_recovery'));
    }
}
