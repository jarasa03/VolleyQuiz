@extends('layouts.app')

@section('title', 'Editar Documento')

@push('body-class', 'admin-page')

@section('content')
    <div class="create-user-container">
        <h1>Editar Documento</h1>

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

        <form action="{{ route('admin.documents.update', $documento->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Título -->
            <div class="form-group">
                <label for="title">Título del Documento</label>
                <input type="text" name="title" id="title" value="{{ old('title', $documento->title) }}"
                    class="form-control" required>
            </div>

            <!-- Sección -->
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

            <!-- Carpeta -->
            <div class="form-group">
                <label for="folder_id">Carpeta (opcional)</label>
                <select name="folder_id" id="folder_id" class="form-control">
                    <option value="">-- Sin carpeta --</option>
                    {{-- Se llenará dinámicamente con JS --}}
                </select>
            </div>

            <!-- Año -->
            <div class="form-group">
                <label for="year">Año (opcional)</label>
                <input type="text" name="year" id="year" value="{{ old('year', $documento->year) }}"
                    class="form-control">
                <small>Ejemplo: 2024, 24-25, etc.</small>
            </div>

            <!-- Archivo PDF (opcional) -->
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sectionSelect = document.getElementById('section_id');
        const folderSelect = document.getElementById('folder_id');
        const selectedFolderId =
        "{{ old('folder_id', $documento->folder_id) }}"; // Se obtiene el valor del folder_id del documento o de la entrada anterior

        const renderOptions = (carpetas, nivel = 0) => {
            carpetas.forEach(carpeta => {
                const option = document.createElement('option');
                option.value = carpeta.id;
                option.textContent = '— '.repeat(nivel) + carpeta.name;

                // Marcar la carpeta seleccionada
                if (carpeta.id == selectedFolderId) {
                    option.selected = true;
                }

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
