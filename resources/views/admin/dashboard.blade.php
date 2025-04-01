@extends('layouts.app')

@section('title', 'Administración')

@push('body-class', 'admin-page')

@section('content')
    <div class="admin-page">
        <div class="admin-container">
            <h1>Panel de Administración</h1>
            <p>Bienvenido, {{ auth()->user()->name }}. Selecciona una opción:</p>

            <div class="admin-menu">
                @if (auth()->user()->isSuperAdmin())
                    <form action="{{ route('admin.users.index') }}" method="GET">
                        <button type="submit" class="admin-button no-select">Administración de Usuarios</button>
                    </form>
                @endif

                <form action="{{ route('admin.questions.index') }}" method="GET">
                    <button type="submit" class="admin-button no-select">Gestión de Preguntas</button>
                </form>

                <form action="{{ route('admin.tags.index') }}" method="GET">
                    <button type="submit" class="admin-button no-select">Gestión de Tags</button>
                </form>

                <form action="{{ route('admin.documents.index') }}" method="GET">
                    <button type="submit" class="admin-button no-select">Gestión de Documentación</button>
                </form>
            </div>
        </div>
    </div>
@endsection
