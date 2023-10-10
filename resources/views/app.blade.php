<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script src="http://localhost:8098"></script>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        {{-- Favicon --}}
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('Favicon/apple-touch-icon.png')}}">
        <link rel="icon" type="image/png" sizes="48x48" href="{{asset('Favicon/favicon-48.png')}}" />
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('Favicon/favicon-32.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('Favicon/favicon-16.png')}}">
        <link rel="manifest" href="{{asset('Favicon/manifest.json')}}">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-gray-100 relative flex flex-col">
        @inertia
    </body>
</html>
