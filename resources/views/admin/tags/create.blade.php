@extends('layouts.app')

@section('title', 'Crear Nuevo Tag')

@push('body-class', 'admin-page')

@section('content')
    <div class="create-user-container">
        <h1>Crear Nuevo Tag</h1>

        <!-- Mostrar los mensajes de sesión -->
        @if (session()->has('error'))
            <div class="alert alert-danger"> <!-- Rojo para errores -->
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning"> <!-- Amarillo para advertencias -->
                {{ session('warning') }}
            </div>
        @endif

        @if (session()->has('message'))
            <div class="alert alert-info"> <!-- Azul para información -->
                {{ session('message') }}
            </div>
        @endif

        <form action="{{ route('admin.tags.store') }}" method="POST">
            @csrf

            <!-- Nombre del Tag -->
            <div class="form-group">
                <label for="name">Nombre del Tag:</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <!-- Selección de color -->
            <div class="form-group">
                <label for="color">Color del Tag:</label>
                <div class="color-picker">
                    <input type="color" name="color" id="color-picker" value="{{ old('color', '#7A7A7A') }}"
                        class="form-control" required>
                    <button type="button" id="randomColorBtn" class="btn random-color">🎨 Aleatorio</button>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">✅ Crear Tag</button>
                <a href="{{ route('admin.tags.index') }}" class="btn cancel">⬅ Volver</a>
            </div>
        </form>
    </div>
@endsection
