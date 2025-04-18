@extends('layouts.app')

@section('title', 'Documentación - {{ $seccion }}')

@push('body-class', 'documentation-page documentation-folder')

@section('content')
    <div class="documentation-page">
        <div class="documentation-container">
            <h1>Documentación - {{ $seccion }}</h1>

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

                {{-- 📄 DOCUMENTOS PDF --}}
                @forelse ($documentos as $documento)
                    <a href="{{ Storage::url($documento->file_path) }}" class="folder-tile no-select" target="_blank">
                        <img src="{{ asset('images/pdf-icon.png') }}" alt="PDF" class="pdf-icon">
                        <span class="folder-name">{{ $documento->title }}</span>
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
