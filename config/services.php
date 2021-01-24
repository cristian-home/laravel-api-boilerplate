<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Passport
    'passport' => [
        'oauth' => [
            'endpoint' => env('OAUTH_ENDPOINT'),
            'expiration' => [
                'access_token' => env('OAUTH_ACCESS_TOKEN_EXPIRE_MINUTES', 10),
                'refresh_token' => env(
                    'OAUTH_REFRESH_TOKEN_EXPIRE_MINUTES',
                    60 * 24 * 60,
                ),
            ],
            'clients' => [
                'webapp' => [
                    'name' => env('OAUTH_WEB_APP_CLIENT_NAME', 'WebApp'),
                ],
            ],
        ],
    ],
];
