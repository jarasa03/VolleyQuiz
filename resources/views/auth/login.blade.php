<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    @vite(['resources/scss/app.scss'])
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>

        <!-- Mostrar mensaje de error si Laravel redirigió aquí -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Mostrar mensaje de sesión si existe -->
        @if (session('message'))
            <div class="alert alert-warning">
                {{ session('message') }}
            </div>
        @endif

        <!-- Mostrar errores de validación -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('auth.login.post') }}" method="POST">
            @csrf
            <label for="name">Nombre de Usuario:</label>
            <input type="text" id="name" name="name" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>
</html>
