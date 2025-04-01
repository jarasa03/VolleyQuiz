@extends('layouts.app')

@section('title', 'Administración de Documentos')

@push('body-class', 'admin-users-page')

@section('content')
    <div class="admin-users-container">
        <h1>Administración de Documentos</h1>

        <!-- Mostrar los mensajes de sesión -->
        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if (session()->has('message'))
            <div class="alert alert-info">
                {{ session('message') }}
            </div>
        @endif

        <!-- Botón para crear un nuevo documento -->
        <form action="{{ route('admin.documents.create') }}" method="GET">
            <button type="submit" class="btn add-user">+ Subir Nuevo Documento</button>
        </form>

        <!-- 🔍 Formulario de Búsqueda -->
        <form method="GET" action="{{ route('admin.documents.index') }}" class="search-form">
            <input type="text" name="search" placeholder="Buscar por título o sección" value="{{ request('search') }}">
            <button type="submit">🔍 Buscar</button>
        </form>

        @if (request('search'))
            <p>Resultados para "<strong>{{ request('search') }}</strong>": {{ $documentos->total() }} encontrados</p>
            <form action="{{ route('admin.documents.index') }}" method="GET">
                <button type="submit" class="btn clear-search">❌ Limpiar Búsqueda</button>
            </form>
        @endif

        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Sección</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documentos as $documento)
                    <tr>
                        <td>{{ $documento->id }}</td>
                        <td>{{ $documento->title }}</td>
                        <td>{{ ucfirst($documento->section->name) }}</td>
                        <td class="action-buttons">
                            <form action="{{ route('admin.documents.edit', $documento->id) }}" method="GET">
                                <button type="submit" class="btn edit">✏️ Editar</button>
                            </form>
                            <form action="{{ route('admin.documents.destroy', $documento->id) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que quieres eliminar este documento?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn delete">🗑 Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Mostrar los enlaces de paginación -->
        <div class="pagination">
            {{ $documentos->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
