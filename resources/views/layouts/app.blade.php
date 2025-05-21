<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VolleyQuiz')</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>

@php
    $bodyClasses = [];

    // Agregar clase "has-navbar" solo si no estamos en rutas de autenticación
    if (
        !request()->routeIs('auth.login') &&
        !request()->routeIs('auth.register') &&
        !request()->routeIs('password.request') &&
        !request()->routeIs('password.reset')
    ) {
        $bodyClasses[] = 'has-navbar';
    }
@endphp

<body class="{{ implode(' ', $bodyClasses) }} @stack('body-class')">

    <!-- Mostrar navbar solo si NO estamos en las rutas de autenticación -->
    @unless (request()->routeIs('auth.login') ||
            request()->routeIs('auth.register') ||
            request()->routeIs('password.request') ||
            request()->routeIs('password.reset'))
        @include('layouts.navbar')
    @endunless

    <div class="container">
        @yield('content')
    </div>

</body>

</html>
