@extends('layouts.app')

@section('title', 'Administración de Preguntas')

@push('body-class', 'admin-users-page')

@section('content')
    <div class="admin-users-container">
        <h1>Administración de Preguntas</h1>

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

        <!-- Botón para crear una nueva pregunta -->
        <form action="{{ route('admin.questions.create') }}" method="GET">
            <button type="submit" class="btn add-user">+ Crear Nueva Pregunta</button>
        </form>

        <!-- 🔍 Formulario de Búsqueda -->
        <form method="GET" action="{{ route('admin.questions.index') }}" class="search-form">
            <input type="text" name="search" placeholder="Buscar por texto de pregunta" value="{{ request('search') }}">
            <button type="submit">🔍 Buscar</button>
        </form>

        @if (request('search'))
            <p>Resultados para "<strong>{{ request('search') }}</strong>": {{ $questions->total() }} encontrados</p>
        @endif

        @if (request('search'))
            <form action="{{ route('admin.questions.index') }}" method="GET">
                <button type="submit" class="btn clear-search">❌ Limpiar Búsqueda</button>
            </form>
        @endif

        <!-- Tabla de preguntas -->
        <table class="user-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Texto de Pregunta</th>
                    <th style="width: 100px;">Categoría</th>
                    <th style="width: 600px;">Tags</th>
                    <th style="width: 200px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questions as $question)
                    <tr>
                        <td>{{ $question->id }}</td>
                        <td>{{ $question->question_text }}</td>
                        <td>{{ $question->category->name ?? 'Sin categoría' }}</td>
                        <td>
                            @foreach ($question->tags as $tag)
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
                            @endforeach
                        </td>
                        <td class="action-buttons">
                            <form action="{{ route('admin.questions.edit', $question->id) }}" method="GET">
                                <button type="submit" class="btn edit">✏️ Editar</button>
                            </form>
                            <form action="{{ route('admin.questions.delete', $question->id) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que quieres eliminar esta pregunta?');">
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
            {{ $questions->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection
