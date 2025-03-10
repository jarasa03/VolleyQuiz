<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error 405 - Método no permitido</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="container">
        <h1>🚫 Error 405</h1>
        <p>El método de solicitud no está permitido en esta página.</p>
        <a href="{{ url('/') }}">Volver al inicio</a>
    </div>
</body>
</html>