@extends('layouts.app')

@section('title', 'Registro')

@push('body-class', 'auth-page')

@section('content')
    <div class="auth-container">
        <h1 class="no-select">Registro</h1>

        <!-- Mensajes de error -->
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Mensaje de éxito si se registra correctamente -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('auth.register.post') }}" method="POST">
            @csrf
            <label for="name" class="no-select">Nombre de Usuario:</label>
            <input type="text" id="name" name="name" required>

            <label for="email" class="no-select">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password" class="no-select">Contraseña:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <span class="toggle-password no-select" onclick="togglePasswordVisibility('password')">👁️</span>
            </div>

            <label for="password_confirmation" class="no-select">Confirmar Contraseña:</label>
            <div class="password-container">
                <input type="password" id="password_confirmation" name="password_confirmation" required>
                <span class="toggle-password no-select"
                    onclick="togglePasswordVisibility('password_confirmation')">👁️</span>
            </div>

            <button type="submit" class="no-select">Registrarse</button>
        </form>

        <p class="no-select">¿Ya tienes cuenta? <a href="{{ route('auth.login') }}">Inicia sesión aquí</a></p>
    </div>
@endsection
