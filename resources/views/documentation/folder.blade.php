@extends('layouts.app')

@section('title', 'Documentación - {{ $seccion }}')

@push('body-class', 'documentation-page documentation-folder')

@section('content')
    <div class="documentation-page">
        <div class="documentation-container" style="height: auto;
    min-height: 470px;">
            <div class="documentation-header"
                style="display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; margin-bottom: 1rem;">

                <div></div> <!-- Columna vacía para empujar el título al centro -->

                <h1 style="margin: 0; text-align: center;">Documentación - {{ $seccion }}</h1>

                <div style="text-align: right;">
                    <a href="{{ route('documentacion.dashboard') }}" class="btn doc-back-button"
                        style="background-color: #f4d35e;
    color: black;
    font-weight: 500;
    border: none;
    border-radius: 1rem;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.3s ease;">
                        ←Volver
                    </a>
                </div>
            </div>


            @if (isset($breadcrumb))
                <div class="breadcrumb">
                    <a href="{{ route('documentacion.seccion', ['seccion' => strtolower($section->name)]) }}">🏠
                        {{ strtoupper($section->name) }}</a>
                    @foreach ($breadcrumb as $crumb)
                        &nbsp;/&nbsp;
                        <a href="{{ route('documentacion.carpeta', $crumb->id) }}">{{ $crumb->name }}</a>
                    @endforeach
                </div>
            @endif

            <div class="folder-grid-container">
                {{-- 🔙 BOTÓN VOLVER ATRÁS --}}
                @if ($carpeta)
                    <a href="{{ $carpeta->parent
                        ? route('documentacion.carpeta', $carpeta->parent->id)
                        : route('documentacion.seccion', ['seccion' => strtolower($section->name)]) }}"
                        class="folder-tile no-select" title="Volver a la carpeta anterior">
                        <img src="{{ asset('images/back-folder-icon.png') }}" alt="Volver" class="folder-icon">
                        <span class="folder-name">Atrás</span>
                    </a>
                @endif
                {{-- 📁 SUBCARPETAS --}}
                @forelse ($subcarpetas as $carpeta)
                    <a href="{{ route('documentacion.carpeta', $carpeta->id) }}" class="folder-tile no-select">
                        <img src="{{ asset('images/folder-icon.png') }}" alt="Carpeta" class="folder-icon">
                        <span class="folder-name">{{ $carpeta->name }}</span>
                    </a>
                @empty
                    {{-- Nada, por si no hay subcarpetas --}}
                @endforelse

                {{-- 📄 DOCUMENTOS --}}
                @forelse ($documentos as $documento)
                    <a href="{{ Storage::url($documento->file_path) }}" class="folder-tile no-select" target="_blank"
                        title="{{ $documento->title }}">
                        @if (strpos($documento->file_path, '.pdf') !== false)
                            <img src="{{ asset('images/pdf-icon.png') }}" alt="PDF" class="pdf-icon">
                        @elseif (strpos($documento->file_path, '.docx') !== false || strpos($documento->file_path, '.doc') !== false)
                            <img src="{{ asset('images/word-icon.png') }}" alt="Word" class="word-icon">
                        @elseif (strpos($documento->file_path, '.xls') !== false || strpos($documento->file_path, '.xlsx') !== false)
                            <img src="{{ asset('images/excel-icon.png') }}" alt="Excel" class="excel-icon">
                        @endif

                        <!-- Título del documento -->
                        <span class="folder-name">{{ $documento->title }}</span>

                        <!-- Mostrar el año si existe -->
                        @if ($documento->year)
                            <span class="document-year">{{ $documento->year }}</span>
                        @endif
                    </a>
                @empty
                    @if ($subcarpetas->isEmpty())
                        <p>No hay documentos ni carpetas en esta sección.</p>
                    @endif
                @endforelse


            </div>
        </div>
    </div>
@endsection
