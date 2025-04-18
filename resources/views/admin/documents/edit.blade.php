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
                <label for="folder_id">Carpeta (opcional)</label>
                <select name="folder_id" id="folder_id" class="form-control">
                    <option value="">-- Sin carpeta --</option>
                    {{-- Se llenará dinámicamente con JS --}}
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sectionSelect = document.getElementById('section_id');
        const folderSelect = document.getElementById('folder_id');

        function cargarCarpetas(sectionId, selectedFolderId = null) {
            folderSelect.innerHTML = '<option value="">-- Cargando carpetas... --</option>';

            if (!sectionId) {
                folderSelect.innerHTML = '<option value="">-- Sin carpeta --</option>';
                return;
            }

            fetch(`/admin/folders/por-seccion/${sectionId}`)
                .then(response => response.json())
                .then(data => {
                    folderSelect.innerHTML = '<option value="">-- Sin carpeta --</option>';

                    const renderOptions = (carpetas, nivel = 0) => {
                        carpetas.forEach(carpeta => {
                            const option = document.createElement('option');
                            option.value = carpeta.id;
                            option.textContent = '— '.repeat(nivel) + carpeta.name;
                            if (carpeta.id == selectedFolderId) {
                                option.selected = true;
                            }
                            folderSelect.appendChild(option);

                            if (carpeta.children_recursive?.length) {
                                renderOptions(carpeta.children_recursive, nivel + 1);
                            }
                        });
                    };

                    renderOptions(data);
                })
                .catch(() => {
                    folderSelect.innerHTML = '<option value="">-- Error al cargar --</option>';
                });
        }

        // Cargar al iniciar (con carpeta seleccionada si existe)
        cargarCarpetas(sectionSelect.value, `{{ $documento->folder_id }}`);

        // Cargar al cambiar sección
        sectionSelect.addEventListener('change', () => {
            cargarCarpetas(sectionSelect.value);
        });
    });
</script>
