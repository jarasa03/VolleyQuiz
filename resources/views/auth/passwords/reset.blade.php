<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>

<body>
    <div class="container">
        <h1 class="no-select">Restablecer Contraseña</h1>

        @if (session('message'))
            <div class="alert alert-info">{{ session('message') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <label for="email" class="no-select">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password" class="no-select">Nueva Contraseña:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <span class="toggle-password no-select" onclick="togglePasswordVisibility('password')">👁️</span>
            </div>

            <label for="password_confirmation" class="no-select">Confirmar Contraseña:</label>
            <div class="password-container">
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <span class="toggle-password no-select" onclick="togglePasswordVisibility('password_confirmation')">👁️</span>
            </div>

            <button type="submit" class="no-select">Restablecer Contraseña</button>
        </form>

        <p class="no-select"><a href="{{ route('auth.login') }}">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>
