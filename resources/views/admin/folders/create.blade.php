@extends('layouts.app')

@section('title', 'Crear Carpeta')

@push('body-class', 'admin-page')

@section('content')
    <div class="create-user-container">
        <h1>📁 Crear Nueva Carpeta</h1>

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

        <form action="{{ route('admin.folders.store') }}" method="POST">
            @csrf

            <!-- Nombre -->
            <div class="form-group">
                <label for="name">Nombre de la Carpeta</label>
                <input type="text" id="name" name="name" required>
            </div>

            <!-- Sección -->
            <div class="form-group">
                <label for="section_id">Sección</label>
                <select name="section_id" id="section_id" required>
                    <option value="" disabled selected hidden>-- Selecciona una sección --</option>
                    @foreach ($secciones as $seccion)
                        <option value="{{ $seccion->id }}">{{ $seccion->name }}</option>
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
                <button type="submit" class="btn submit">Crear Carpeta</button>
                <a href="{{ route('admin.folders.index') }}" class="btn cancel">Cancelar</a>
            </div>
        </form>
    </div>

    <!-- ✅ Script JS directamente dentro del contenido -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sectionSelect = document.getElementById('section_id');
            const parentSelect = document.getElementById('parent_id');

            sectionSelect.addEventListener('change', function() {
                const sectionId = this.value;
                parentSelect.innerHTML = '<option value="">-- Cargando carpetas... --</option>';

                if (!sectionId) {
                    parentSelect.innerHTML = '<option value="">-- Sin carpeta padre --</option>';
                    return;
                }

                fetch(`/admin/folders/por-seccion/${sectionId}`)
                    .then(response => {
                        return response.json();
                    })
                    .then(data => {
                        parentSelect.innerHTML = '<option value="">-- Sin carpeta padre --</option>';

                        const renderOptions = (carpetas, nivel = 0) => {
                            carpetas.forEach(carpeta => {
                                const option = document.createElement('option');
                                option.value = carpeta.id;
                                option.textContent = '— '.repeat(nivel) + carpeta.name;
                                parentSelect.appendChild(option);

                                if (carpeta.children_recursive && carpeta.children_recursive
                                    .length > 0) {
                                    renderOptions(carpeta.children_recursive, nivel + 1);
                                }
                            });
                        };

                        renderOptions(data);
                    })
                    .catch(err => {
                        parentSelect.innerHTML = '<option value="">-- Error al cargar --</option>';
                    });
            });
        });
    </script>
@endsection
