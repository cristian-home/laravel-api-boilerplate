<?php

namespace App\Traits\Auth;

use Illuminate\Http\Request;
use Laravel\Passport\Client;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

trait OAuthProxy
{
    /**
     * @Author: Cristian David Home
     * @Date: 2021-01-04 20:21:22
     * @Desc: Envía una petición al servidor OAuth.
     * Grants: password, refresh_token
     *
     * @param string $grantType el tipo de Grant que se solicita
     * @param array $data los datos que se envian al servidor
     * @return \Illuminate\Http\JsonResponse
     */
    private function proxy(string $grantType, array $data = [])
    {
        // Obtener el nombre del cliente OAuth
        $clientName = config('services.passport.oauth.clients.webapp.name');

        // Validar que el cliente exista
        Validator::make(
            ['cliente' => $clientName],
            [
                'cliente' => 'required|exists:oauth_clients,name',
            ],
            [
                'required' => __('No authentication client specified'),
                'exists' => __('Invalid authentication client'),
            ],
        )->validate();

        // Obtener el ultimo cliente OAuth creado con el nombre proporcionado
        $client = Client::where('name', $clientName)
            ->orderBy('created_at', 'desc')
            ->first();

        // Crear array de datos
        $data = array_merge($data, [
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => $grantType,
        ]);

        // Crear Request OAuth
        $passportRequest = Request::create(
            url(config('services.passport.oauth.endpoint')),
            'POST',
            $data,
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/json',
            ],
        );

        // Generar respuesta
        $response = app()->handle($passportRequest);

        // Mostrar excepciones si ocurrieron
        switch (true) {
            case $response->status() >= 400:
                return response()->json(
                    json_decode($response->getContent()),
                    $response->status(),
                );
                break;

            case $response->status() >= 500:
                abort(503, __('The authentication server is not responding'));
                break;

            default:
                # code...
                break;
        }

        // Decodificar respuesta
        $oauthData = json_decode($response->getContent());

        // Crear cookie para el refresh token
        $refreshTokenCookie = Cookie::make(
            'refresh-token',
            $oauthData->refresh_token,
            config('services.passport.oauth.expiration.refresh_token'),
            null,
            null,
            false,
            true, // HttpOnly
        );

        // Retornar respuesta con tokens
        return response()
            ->json([
                'token_type' => $oauthData->token_type,
                'expires_in' => $oauthData->expires_in,
                'access_token' => $oauthData->access_token,
            ])
            ->withCookie($refreshTokenCookie);
    }
}
