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

            <!-- Carpeta -->
            <div class="form-group">
                <label for="folder_id">Carpeta (opcional)</label>
                <select name="folder_id" id="folder_id">
                    <option value="">-- Sin carpeta --</option>
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sectionSelect = document.getElementById('section_id');
        const folderSelect = document.getElementById('folder_id');

        const renderOptions = (carpetas, nivel = 0) => {
            carpetas.forEach(carpeta => {
                const option = document.createElement('option');
                option.value = carpeta.id;
                option.textContent = '— '.repeat(nivel) + carpeta.name;
                folderSelect.appendChild(option);

                if (carpeta.children_recursive && carpeta.children_recursive.length > 0) {
                    renderOptions(carpeta.children_recursive, nivel + 1);
                }
            });
        };

        sectionSelect.addEventListener('change', function() {
            const sectionId = this.value;
            folderSelect.innerHTML = '<option value="">-- Cargando carpetas... --</option>';

            if (!sectionId) return;

            fetch(`/admin/folders/por-seccion/${sectionId}`)
                .then(response => response.json())
                .then(data => {
                    folderSelect.innerHTML = '<option value="">-- Sin carpeta --</option>';
                    renderOptions(data);
                })
                .catch(() => {
                    folderSelect.innerHTML =
                        '<option value="">-- Error al cargar carpetas --</option>';
                });
        });

        // Trigger inicial si ya hay una sección seleccionada
        if (sectionSelect.value) {
            sectionSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
