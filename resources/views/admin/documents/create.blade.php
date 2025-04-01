@extends('layouts.app')

@section('title', 'Subir Nuevo Documento')

@push('body-class', 'admin-page')

@section('content')
    <div class="create-user-container">
        <h1>Subir Nuevo Documento</h1>

        <!-- Mensajes de sesión -->
        @if (session('message'))
            <div class="alert alert-info">{{ session('message') }}</div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning">{{ session('warning') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul style="margin: 0; padding-left: 1.2rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Título -->
            <div class="form-group">
                <label for="title">Título del Documento</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            </div>

            <!-- Sección -->
            <div class="form-group">
                <label for="section_id">Sección</label>
                <select name="section_id" id="section_id" required>
                    @foreach ($secciones as $seccion)
                        <option value="{{ $seccion->id }}" {{ old('section_id') == $seccion->id ? 'selected' : '' }}>
                            {{ ucfirst($seccion->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Archivo -->
            <div class="form-group">
                <label for="file">Archivo PDF</label>
                <input type="file" id="file" name="file" accept=".pdf" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">Subir Documento</button>
                <a href="{{ route('admin.documents.index') }}" class="btn cancel">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
