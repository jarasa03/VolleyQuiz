@extends('layouts.app')

@section('title', 'Editar Carpeta')

@push('body-class', 'admin-page')

@section('content')
    <div class="create-user-container">
        <h1>✏️ Editar Carpeta: {{ $carpeta->name }}</h1>

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

        <form action="{{ route('admin.folders.update', $carpeta->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nombre -->
            <div class="form-group">
                <label for="name">Nombre de la Carpeta</label>
                <input type="text" id="name" name="name" value="{{ old('name', $carpeta->name) }}" required>
            </div>

            <!-- Sección -->
            <div class="form-group">
                <label for="section_id">Sección</label>
                <select name="section_id" id="section_id" required>
                    <option value="" disabled hidden>-- Selecciona una sección --</option>
                    @foreach ($secciones as $seccion)
                        <option value="{{ $seccion->id }}" {{ $carpeta->section_id == $seccion->id ? 'selected' : '' }}>
                            {{ $seccion->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Carpeta padre -->
            <div class="form-group">
                <label for="parent_id">Carpeta Padre (opcional)</label>
                <select name="parent_id" id="parent_id">
                    <option value="">-- Sin carpeta padre --</option>
                    {{-- Se llenará dinámicamente --}}
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">Guardar Cambios</button>
                <a href="{{ route('admin.folders.index') }}" class="btn cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- ✅ Script JS para rellenar carpeta padre -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sectionSelect = document.getElementById('section_id');
            const parentSelect = document.getElementById('parent_id');
            const currentParentId = "{{ $carpeta->parent_id }}";
            const currentCarpetaId = "{{ $carpeta->id }}";

            const loadFolders = (sectionId) => {
                parentSelect.innerHTML = '<option value="">-- Cargando carpetas... --</option>';

                fetch(`/admin/folders/por-seccion/${sectionId}`)
                    .then(response => response.json())
                    .then(data => {
                        parentSelect.innerHTML = '<option value="">-- Sin carpeta padre --</option>';

                        const renderOptions = (carpetas, nivel = 0) => {
                            carpetas.forEach(carpeta => {
                                if (carpeta.id == currentCarpetaId)
                            return; // no se puede ser padre de sí misma

                                const option = document.createElement('option');
                                option.value = carpeta.id;
                                option.textContent = '— '.repeat(nivel) + carpeta.name;

                                if (carpeta.id == currentParentId) {
                                    option.selected = true;
                                }

                                parentSelect.appendChild(option);

                                if (carpeta.children_recursive?.length) {
                                    renderOptions(carpeta.children_recursive, nivel + 1);
                                }
                            });
                        };

                        renderOptions(data);
                    })
                    .catch(() => {
                        parentSelect.innerHTML = '<option value="">-- Error al cargar --</option>';
                    });
            };

            if (sectionSelect.value) {
                loadFolders(sectionSelect.value);
            }

            sectionSelect.addEventListener('change', function() {
                loadFolders(this.value);
            });
        });
    </script>
@endsection
