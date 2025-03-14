@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@push('body-class', 'auth-page')

@section('content')
    <div class="auth-container">
        <h1 class="no-select">Iniciar Sesión</h1>

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
            <label for="name" class="no-select">Nombre de Usuario:</label>
            <input type="text" id="name" name="name" required>

            <label for="password" class="no-select">Contraseña:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <span class="toggle-password no-select" onclick="togglePasswordVisibility('password')">👁️</span>
            </div>

            <button type="submit" class="no-select">Iniciar Sesión</button>
        </form>

        <p class="no-select">¿No tienes cuenta? <a href="{{ route('auth.register') }}">Regístrate aquí</a></p>
        <p class="no-select"><a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a></p>
    </div>
@endsection
