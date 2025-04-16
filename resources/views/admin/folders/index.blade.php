@extends('layouts.app')

@section('title', 'Gestión de Carpetas')

@push('body-class', 'admin-users-page')

@section('content')
    <div class="admin-users-container folders-page">
        <h1>📁 Gestión de Carpetas de Documentación</h1>

        <!-- Mensajes de sesión -->
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        @if (session()->has('message'))
            <div class="alert alert-info">{{ session('message') }}</div>
        @endif

        <!-- Botón de nueva carpeta -->
        <form action="{{ route('admin.folders.create') }}" method="GET">
            <button type="submit" class="btn add-user">+ Crear Nueva Carpeta</button>
        </form>

        <!-- 🔍 Buscador -->
        <form method="GET" action="{{ route('admin.folders.index') }}" class="search-form">
            <input type="text" name="search" placeholder="Buscar carpeta..." value="{{ request('search') }}">
            <button type="submit">🔍 Buscar</button>
        </form>

        @if (request('search'))
            <p>Resultados para "<strong>{{ request('search') }}</strong>": {{ $carpetas->count() }} encontrados</p>
            <form action="{{ route('admin.folders.index') }}" method="GET">
                <button type="submit" class="btn clear-search">❌ Limpiar Búsqueda</button>
            </form>
        @endif

        <!-- Lista recursiva -->
        <ul id="folders-tree" class="folder-tree">
            @foreach ($carpetas as $carpeta)
                @include('admin.folders.partials.folder', ['carpeta' => $carpeta, 'nivel' => 0])
            @endforeach
        </ul>
    </div>
@endsection
