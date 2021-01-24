<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Traits\Auth\OAuthProxy;
use App\Http\Controllers\Controller;
use App\Traits\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers, OAuthProxy;

    /**
     * Constructor de la nueva instancia del controlador
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('to.lower')->only(['login']);
        $this->middleware('auth:api')->only(['logout']);
        $this->middleware('guest:api')->except([
            'logout',
            'refreshAccessToken',
        ]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        // Limpiar contador de intentos de incio de sesión
        $this->clearLoginAttempts($request);

        // Hacer petición al proxy y devolver los tokens
        return $this->proxy('password', [
            'username' => $request->email,
            'password' => $request->password,
        ]);
    }

    /**
     * Renovar token de acceso y devolverlo.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function refreshAccessToken(Request $request)
    {
        if ($request->hasCookie('refresh-token')) {
            return $this->proxy('refresh_token', [
                'refresh_token' => $request->cookie('refresh-token'),
            ]);
        }

        return response()->json(
            ['message' => __('Invalid refresh token')],
            401,
        );
    }
}
