@extends('layouts.app')

@section('title', 'Editar Documento')

@push('body-class', 'admin-page')

@section('content')
    <div class="edit-user-container">
        <h1>Editar Documento</h1>

        <form action="{{ route('admin.documents.update', $documento->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="title">Título del Documento</label>
                <input type="text" name="title" id="title" value="{{ old('title', $documento->title) }}"
                    class="form-control" required>
            </div>

            <div class="form-group">
                <label for="section_id">Sección</label>
                <select name="section_id" id="section_id" class="form-control" required>
                    @foreach ($secciones as $seccion)
                        <option value="{{ $seccion->id }}" {{ $documento->section_id == $seccion->id ? 'selected' : '' }}>
                            {{ $seccion->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="file">Archivo PDF (opcional)</label>
                <input type="file" name="file" id="file" class="form-control">
                <small>Deja este campo vacío si no quieres reemplazar el archivo actual.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">Actualizar Documento</button>
                <a href="{{ route('admin.documents.index') }}" class="btn cancel">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
