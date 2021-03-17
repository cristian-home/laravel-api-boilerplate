<?php

use Custom\OTP\OTPConstants;

return [
    /*
     * Enable / disable Google2FA.
     */
    'enabled' => env('OTP_ENABLED', true),

    /*
     * Lifetime in minutes.
     *
     * In case you need your users to be asked for a new one time passwords from time to time.
     */
    'lifetime' => (int) env('OTP_LIFETIME', 0), // 0 = eternal

    /*
     * Renew lifetime at every new request.
     */
    'keep_alive' => env('OTP_KEEP_ALIVE', true),

    /*
     * Auth container binding.
     */
    'auth' => 'auth',

    /*
     * Guard.
     */
    'guard' => 'web',

    /*
     * 2FA verified session var.
     */

    'session_var' => 'app2fa',

    /*
     * One Time Password request input name.
     */
    'otp_input' => OTPConstants::OTP_INPUT_FIELD,

    /*
     * Recovery Code request input name.
     */
    'recovery_code_input' => OTPConstants::RECOVERY_CODE_INPUT_FIELD,

    /*
     * One Time Password Window.
     */
    'window' => 1,

    /*
     * Forbid user to reuse One Time Passwords.
     */
    'forbid_old_passwords' => false,

    /*
     * User's table column for google2fa enabled.
     */
    'otp_enabled_column' => OTPConstants::OTP_ENABLED_COLUMN,

    /*
     * User's table column for google2fa secret.
     */
    'otp_secret_column' => OTPConstants::OTP_SECRET_COLUMN,

    /*
     * User's table column for google2fa recovery codes.
     */
    'otp_recovery_codes_column' => OTPConstants::OTP_RECOVERY_CODES_COLUMN,

    /*
     * One Time Password View.
     */
    'view' => 'auth.2fa.index',

    /*
     * One Time Password error message.
     */
    'error_messages' => [
        'wrong_otp' => "The 'One Time Password' typed was wrong.",
        'wrong_recovery' => "The 'Recovery Code' typed was wrong.",
        'cannot_be_empty' => 'One Time Password cannot be empty.',
        'unknown' => 'An unknown error has occurred. Please try again.',
    ],

    /*
     * Throw exceptions or just fire events?
     */
    'throw_exceptions' => env('OTP_THROW_EXCEPTION', true),

    /*
     * Which image backend to use for generating QR codes?
     *
     * Supports imagemagick, svg and eps
     */
    'qrcode_image_backend' =>
        \PragmaRX\Google2FALaravel\Support\Constants::QRCODE_IMAGE_BACKEND_SVG,
];
