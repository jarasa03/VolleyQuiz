@extends('layouts.app')

@section('title', 'Documentación General')

@push('body-class', 'documentation-page')

@section('content')
    <div class="documentation-page">
        <div class="documentation-container">
            <h1>Documentación General</h1>
            <p>Consulta documentos generales que aplican a todas las federaciones o aspectos comunes del voleibol.</p>

            <div class="document-list">
                {{-- Aquí deberías iterar los documentos desde el controlador --}}
                @foreach ($documentos as $documento)
                    <div class="document-card">
                        <div class="document-title">{{ $documento->title }}</div>
                        <div class="document-actions">
                            <a href="{{ route('documentos.download', $documento->id) }}" class="doc-button">📥 Descargar</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
