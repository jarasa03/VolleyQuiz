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

        <!-- Mostrar mensaje de éxito si existe en la sesión -->
        @if (session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        <!-- Mostrar mensaje de error si existe en la sesión -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <span>❌</span>
                <p>{{ $errors->first() }}</p>
            </div>
        @endif

        <form action="{{ route('auth.login.post') }}" method="POST">
            @csrf
            <label for="name">Nombre de Usuario:</label>
            <input type="text" id="name" name="name" required value="{{ old('name') }}">

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>

</html>
