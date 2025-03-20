@extends('layouts.app')

@section('title', 'Crear Nuevo Usuario')

@push('body-class', 'admin-page')

@section('content')
    <div class="create-user-container">
        <h1>Crear Nuevo Usuario</h1>

        <!-- Mostrar mensajes de sesión -->
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

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <!-- Nombre -->
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" required>
            </div>

            <!-- Correo Electrónico -->
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Contraseña -->
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-group">
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password no-select" onclick="togglePasswordVisibility('password')">👁️</span>
                </div>
            </div>

            <!-- Confirmar Contraseña -->
            <div class="form-group">
                <label for="password_confirmation">Confirmar Contraseña</label>
                <div class="password-group">
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                    <span class="toggle-password no-select"
                        onclick="togglePasswordVisibility('password_confirmation')">👁️</span>
                </div>
            </div>

            <!-- Rol -->
            <div class="form-group">
                <label for="role">Rol</label>
                <select name="role" id="role" required>
                    <option value="user">Usuario</option>
                    <option value="admin">Administrador</option>
                    <option value="superadmin">Superadmin</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">Crear Usuario</button>
                <a href="{{ route('admin.users.index') }}" class="btn cancel">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
