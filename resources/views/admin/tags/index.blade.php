@extends('layouts.app')

@section('title', 'Administración de Tags')

@push('body-class', 'admin-users-page')

@section('content')
    <div class="admin-users-container">
        <h1>Administración de Tags</h1>

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

        <!-- Botón para crear un nuevo usuario -->
        <form action="{{ route('admin.tags.create') }}" method="GET">
            <button type="submit" class="btn add-user">+ Crear Nuevo Tag</button>
        </form>

        <!-- 🔍 Formulario de Búsqueda -->
        <form method="GET" action="{{ route('admin.tags.index') }}" class="search-form">
            <input type="text" name="search" placeholder="Buscar por nombre" value="{{ request('search') }}">
            <button type="submit">🔍 Buscar</button>
        </form>

        @if (request('search'))
            <p>Resultados para "<strong>{{ request('search') }}</strong>": {{ $tags->total() }} encontrados</p>
        @endif

        @if (request('search'))
            <form action="{{ route('admin.tags.index') }}" method="GET">
                <button type="submit" class="btn clear-search">❌ Limpiar Búsqueda</button>
            </form>
        @endif


        <!-- Tabla de tags -->
        <table class="user-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tags as $tag)
                    <tr>
                        <td>{{ $tag->id }}</td>
                        <td>
                            <span class="tag-label"
                                style="
                                color: {{ $tag->color }};
                                border: 2px solid {{ $tag->color }};
                                background-color: {{ $tag->color }}20; /* Hace el fondo más claro */
                                padding: 5px 10px;
                                border-radius: 20px;
                                font-weight: bold;
                                display: inline-block;
                            ">
                                {{ $tag->name }}
                            </span>
                        </td>
                        <td class="action-buttons">
                            <form action="{{ route('admin.tags.edit', $tag->id) }}" method="GET">
                                <button type="submit" class="btn edit">✏️ Editar</button>
                            </form>
                            <form action="{{ route('admin.tags.delete', $tag->id) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que quieres eliminar este tag?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn delete">🗑 Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Paginación -->
        <div class="pagination">
            {{ $tags->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
