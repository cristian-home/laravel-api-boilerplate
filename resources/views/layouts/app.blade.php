<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
        />
        <title>{{ config('app.name', 'Laravel') }}</title>

        {{--
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="user" content="{{ Auth::user() }}" />
        --}}

        <!-- Favicon -->
        <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="{{ asset('images/favicon/apple-touch-icon.png') }}"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="{{ asset('images/favicon/favicon-32x32.png') }}"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="{{ asset('images/favicon/favicon-16x16.png') }}"
        />
        <link
            rel="manifest"
            href="{{ asset('images/favicon/site.webmanifest') }}"
        />
        <link
            rel="mask-icon"
            href="{{ asset('images/favicon/safari-pinned-tab.svg') }}"
            color="#ff2d20"
        />
        <link
            rel="shortcut icon"
            href="{{ asset('images/favicon/favicon.ico') }}"
        />
        <meta name="msapplication-TileColor" content="#ff2d20" />
        <meta
            name="msapplication-config"
            content="{{ asset('images/favicon/browserconfig.xml') }}"
        />
        <meta name="theme-color" content="#ffffff" />

        <!-- Mix Manifest -->
        <link rel="manifest" href="/mix-manifest.json" />

        <!-- Styles -->
        <link href="{{ mix('css/app.css') }}" rel="stylesheet" />

        @yield('head')
    </head>

    <body>
        @yield('content')

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </body>
</html>
