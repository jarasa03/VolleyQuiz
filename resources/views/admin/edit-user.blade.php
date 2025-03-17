@extends('layouts.app')

@section('title', 'Editar Usuario')

@push('body-class', 'admin-page')

@section('content')
    <div class="edit-user-container">
        <h1>Editar Usuario</h1>
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" name="name" id="name" value="{{ $user->name }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}" class="form-control"
                    required>
            </div>

            <div class="form-group">
                <label for="role">Rol</label>
                <select name="role" id="role" class="form-control" required>
                    <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Usuario</option>
                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrador</option>
                    <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">Actualizar Usuario</button>
                <a href="{{ route('admin.users') }}" class="btn cancel">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
