@extends('layouts.app')

@section('title', 'Administración de Usuarios')

@push('body-class', 'admin-users-page')

@section('content')
    <div class="admin-users-container">
        <h1>Administración de Usuarios</h1>

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
        <form action="{{ route('admin.users.create') }}" method="GET">
            <button type="submit" class="btn add-user">+ Crear Nuevo Usuario</button>
        </form>

        <!-- 🔍 Formulario de Búsqueda -->
        <form method="GET" action="{{ route('admin.users') }}" class="search-form">
            <input type="text" name="search" placeholder="Buscar por nombre o email" value="{{ request('search') }}">
            <button type="submit">🔍 Buscar</button>
        </form>

        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td class="action-buttons">
                            <form action="{{ route('admin.users.edit', $user->id) }}" method="GET">
                                <button type="submit" class="btn edit">✏️ Editar</button>
                            </form>
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que quieres eliminar este usuario?');">
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
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
