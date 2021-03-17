<?php

namespace App\Rules;

use Google2FA;
use Illuminate\Contracts\Validation\Rule;

class ValidOTP implements Rule
{
    protected $secret;

    /**
     * Create a new rule instance.
     *
     * @param  string  $secret
     * @return void
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
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
        return Google2FA::verifyGoogle2FA($this->secret, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __(config('google2fa.error_messages.wrong_otp'));
    }
}
