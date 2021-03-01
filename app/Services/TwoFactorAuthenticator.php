<?php

namespace App\Services;

use Exception;
use Validator;
use Custom\OTP\OTPConstants;
use Illuminate\Http\JsonResponse;
use PragmaRX\Google2FALaravel\Support\Constants;
use PragmaRX\Google2FALaravel\Events\LoginFailed;
use PragmaRX\Google2FALaravel\Events\LoginSucceeded;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use PragmaRX\Google2FALaravel\Exceptions\InvalidSecretKey;
use PragmaRX\Google2FALaravel\Events\OneTimePasswordRequested;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class TwoFactorAuthenticator extends Authenticator
{
    /**
     * Check if the current use is authenticated via OTP.
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->canPassWithoutCheckingOTP() ||
            $this->checkOTP() === Constants::OTP_VALID ||
            $this->checkValidRecoveryCode();
    }

    /**
     * Check if it is already logged in or passable without checking for an OTP.
     *
     * @return bool
     */
    protected function canPassWithoutCheckingOTP()
    {
        return !$this->isEnabled() ||
            $this->noUserIsAuthenticated() ||
            !$this->isActivated() ||
            $this->twoFactorAuthStillValid();
    }

    /**
     * Check if no user is authenticated using OTP.
     *
     * @return bool
     */
    public function noUserIsAuthenticated()
    {
        return is_null($this->getUser());
    }

    /**
     * Get the current user.
     *
     * @return mixed
     */
    public function getUser()
    {
        return request()->user();
    }

    /**
     * Check if the 2FA is activated for the user.
     *
     * @return bool
     */
    public function isActivated()
    {
        return $this->getUser()->{OTPConstants::OTP_SECRET_COLUMN};
    }

    /**
     * Get the user Google2FA secret.
     *
     * @throws InvalidSecretKey
     *
     * @return mixed
     */
    protected function getGoogle2FASecretKey()
    {
        return $this->getUser()->{$this->config('otp_secret_column')};
    }

    /**
     * Check if the input OTP is valid. Returns one of the possible OTP_STATUS codes:
     * 'empty', 'valid' or 'invalid'.
     *
     * @return string
     */
    protected function checkOTP()
    {
        if (
            !$this->inputHasOneTimePassword() ||
            empty($this->getInputOneTimePassword())
        ) {
            return Constants::OTP_EMPTY;
        }

        $isValid = $this->verifyOneTimePassword();

        if ($isValid) {
            $this->login();
            $this->fireLoginEvent($isValid);

            return Constants::OTP_VALID;
        }

        $this->fireLoginEvent($isValid);

        return Constants::OTP_INVALID;
    }

    /**
     * Check if the recovery code is valid.
     *
     * @return bool
     */
    public function checkValidRecoveryCode(string $recovery_code = null)
    {
        $user = $this->getUser();
        $recovery_codes = $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN};
        $input =
            $recovery_code ??
            $this->getRequest()->{OTPConstants::RECOVERY_CODE_INPUT_FIELD};

        if (is_null($input)) {
            return false;
        }

        foreach ($recovery_codes as $key => $code) {
            if ($code == $input) {
                unset($recovery_codes[$key]);

                $user->{OTPConstants::OTP_RECOVERY_CODES_COLUMN} = array_values(
                    $recovery_codes,
                );

                $user->save();

                $this->login();
                $this->fireLoginEvent(true);

                return true;
            }
        }

        return false;
    }

    /**
     * Create a response to request the OTP.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function makeRequestOneTimePasswordResponse()
    {
        event(new OneTimePasswordRequested($this->getUser()));

        return $this->getRequest()->expectsJson()
            ? $this->makeJsonResponse($this->makeStatusCode())
            : $this->makeHtmlResponse($this->makeStatusCode());
    }

    /**
     * Create a response to request the OTP.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function makeFailedLoginOneTimePasswordResponse()
    {
        event(new OneTimePasswordRequested($this->getUser()));

        $validator = Validator::make($this->getRequest()->all(), [
            OTPConstants::OTP_INPUT_FIELD => ['required', 'numeric'],
        ]);

        $validator->after(function ($validator) {
            if (count($validator->errors()) == 0) {
                $validator
                    ->errors()
                    ->add(
                        OTPConstants::OTP_INPUT_FIELD,
                        __(config('google2fa.error_messages.wrong_otp')),
                    );
            }

            $validator
                ->errors()
                ->add(
                    OTPConstants::OTP_REQUIRED_STR,
                    $this->getUser()->{OTPConstants::OTP_ENABLED_COLUMN},
                );
        });

        $validator->validate();
    }

    /**
     * Create a response to request the OTP.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function makeFailedLoginRecoveryCodeResponse()
    {
        // event(new OneTimePasswordRequested($this->getUser()));

        $validator = Validator::make($this->getRequest()->all(), [
            OTPConstants::RECOVERY_CODE_INPUT_FIELD => [
                'required',
                'alpha_dash',
            ],
        ]);

        $validator->after(function ($validator) {
            if (count($validator->errors()) == 0) {
                $validator
                    ->errors()
                    ->add(
                        OTPConstants::RECOVERY_CODE_INPUT_FIELD,
                        __(config('google2fa.error_messages.wrong_recovery')),
                    );
            }

            $validator
                ->errors()
                ->add(
                    OTPConstants::OTP_REQUIRED_STR,
                    $this->getUser()->{OTPConstants::OTP_ENABLED_COLUMN},
                );
        });

        $validator->validate();
    }

    /**
     * Make the status code, to respond accordingly.
     *
     * @return int
     */
    protected function makeStatusCode()
    {
        if (
            $this->getRequest()->isMethod('get') ||
            $this->checkOTP() === Constants::OTP_VALID
        ) {
            return SymfonyResponse::HTTP_OK;
        }

        if ($this->checkOTP() === Constants::OTP_EMPTY) {
            return SymfonyResponse::HTTP_BAD_REQUEST;
        }

        return SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY;
    }

    /**
     * Make a JSON response.
     *
     * @param $statusCode
     *
     * @return JsonResponse
     */
    protected function makeJsonResponse($statusCode)
    {
        return new JsonResponse(
            $this->getErrorBagForStatusCode($statusCode),
            $statusCode,
        );
    }

    /**
     * Get a message bag with a message for a particular status code.
     *
     * @param $statusCode
     *
     * @return MessageBag
     */
    protected function getErrorBagForStatusCode($statusCode)
    {
        $errorMap = [
            SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY =>
                'google2fa.error_messages.wrong_otp',
            SymfonyResponse::HTTP_BAD_REQUEST =>
                'google2fa.error_messages.cannot_be_empty',
        ];

        return $this->createErrorBagForMessage(
            trans(
                config(
                    array_key_exists($statusCode, $errorMap)
                        ? $errorMap[$statusCode]
                        : 'google2fa.error_messages.unknown',
                ),
            ),
        );
    }

    /**
     * Fire login (success or failed).
     *
     * @param $succeeded
     */
    private function fireLoginEvent($succeeded)
    {
        event(
            $succeeded
                ? new LoginSucceeded($this->getUser())
                : new LoginFailed($this->getUser()),
        );

        return $succeeded;
    }
}
