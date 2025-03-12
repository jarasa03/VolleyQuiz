<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    @vite(['resources/scss/app.scss'])
</head>

<body>
    <div class="container">
        <h1 class="no-select">Recuperar Contraseña</h1>

        @if (session('message'))
            <div class="alert alert-info">{{ session('message') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <label for="email" class="no-select">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit" class="no-select">Enviar enlace de recuperación</button>
        </form>

        <p class="no-select"><a href="{{ route('auth.login') }}">Volver al inicio de sesión</a></p>
    </div>
</body>
</html>
