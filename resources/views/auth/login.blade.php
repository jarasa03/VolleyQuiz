<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>

        <!-- Mostrar mensaje de estado si existe en la sesión -->
        @if (isset($message))
            <div class="alert alert-info">
                {{ $message }}
            </div>
        @endif


        <form action="{{ route('auth.login') }}" method="POST">
            @csrf
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>

</html>
