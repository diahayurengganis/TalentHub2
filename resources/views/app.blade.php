<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- 🌟 START: FAVICON & MANIFEST GENERATOR CONFIGURATION -->
        <!-- Ikon Standar Apple Touch (iOS) -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/icon/apple-touch-icon.png') }}">

        <!-- Ikon Browser Standar (Tab PC) -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/icon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/icon/favicon-16x16.png') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/icon/favicon.ico') }}">

        <!-- Pengarah Web Manifest (Android & PWA) -->
        <link rel="manifest" href="{{ asset('assets/icon/site.webmanifest') }}">

        <!-- Warna Tema Browser Mobile -->
        <meta name="theme-color" content="#1e3a8a">
        <!-- 🌟 END: FAVICON CONFIGURATION -->

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />



        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
