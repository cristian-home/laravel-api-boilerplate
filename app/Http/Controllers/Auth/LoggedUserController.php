<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Cookie;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\RefreshTokenRepository;

class LoggedUserController extends Controller
{
    /**
     * Constructor de la nueva instancia del controlador
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('verified')->only(['checkAuth']);
    }

    /**
     * Checkear autenticación.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkAuth()
    {
        return response()->json(null, 204);
    }

    /**
     * Obtener usuario actual.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCurrentUser(Request $request)
    {
        $user = $request->user();
        return new UserResource($user);
    }

    /**
     * Cerrar la sesión del usuario en la aplicación.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Repositortios de passport
        $tokenRepository = app(TokenRepository::class);
        $refreshTokenRepository = app(RefreshTokenRepository::class);

        // Obtener el Access Token
        $accessToken = $request->user()->token();
        // Revocar el access token...
        $tokenRepository->revokeAccessToken($accessToken->id);
        // Revocar el refresh token...
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId(
            $accessToken->id,
        );
        // Olvidar cookie de refresh token
        $cookie = Cookie::forget('refresh-token');

        return response()
            ->json(null, 204)
            ->withCookie($cookie);
    }
}
