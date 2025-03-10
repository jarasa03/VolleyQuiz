<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h1>Bienvenido, {{ auth()->user()->name }} 🎉</h1>
    <form action="{{ route('auth.logout') }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>    
</body>
</html>
