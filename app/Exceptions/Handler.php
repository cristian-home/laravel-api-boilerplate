<?php

namespace App\Exceptions;

use Arr;
use Throwable;
use Custom\OTP\OTPConstants;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated(
        $request,
        AuthenticationException $exception
    ) {
        return $request->expectsJson()
            ? response()->json(['message' => __($exception->getMessage())], 401)
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $errors_msg = $exception->errors();

        $data = [];

        if (Arr::has($errors_msg, OTPConstants::OTP_REQUIRED_STR)) {
            $errors = Arr::pull($errors_msg, OTPConstants::OTP_REQUIRED_STR);
            $data[OTPConstants::OTP_REQUIRED_STR] = $errors[0];
        }

        $data['message'] = __($exception->getMessage());
        $data['errors'] = $errors_msg;

        return response()->json($data, $exception->status);
    }
}
