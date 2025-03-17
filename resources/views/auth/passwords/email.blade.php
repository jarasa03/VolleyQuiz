@extends('layouts.app')

@section('title', 'Recuperar Contraseña')

@push('body-class', 'auth-page')

@section('content')
    <div class="auth-container">
        <h1 class="no-select">Recuperar Contraseña</h1>

        @if (session('message'))
            <div class="alert alert-info"> <!-- Azul para info -->
                {{ session('message') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning"> <!-- Amarillo para advertencias -->
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger"> <!-- Rojo para errores -->
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <label for="email" class="no-select">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>
            <button type="submit" class="no-select">Enviar enlace de recuperación</button>
        </form>

        <p class="no-select"><a href="{{ route('auth.login') }}">Volver al inicio de sesión</a></p>
    </div>
@endsection
