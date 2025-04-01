@extends('layouts.app')

@section('title', 'Documentación')

@push('body-class', 'documentation-page')

@section('content')
    <div class="documentation-page">
        <div class="documentation-container">
            <h1>Documentación</h1>
            <p>Consulta los reglamentos y guías oficiales organizados por federación o categoría:</p>

            <div class="documentation-menu">
                <form action="{{ route('documentacion.seccion', ['seccion' => 'general']) }}" method="GET">
                    <button type="submit" class="doc-button no-select">General</button>
                </form>

                <form action="{{ route('documentacion.seccion', ['seccion' => 'fivb']) }}" method="GET">
                    <button type="submit" class="doc-button no-select">FIVB</button>
                </form>

                <form action="{{ route('documentacion.seccion', ['seccion' => 'rfevb']) }}" method="GET">
                    <button type="submit" class="doc-button no-select">RFEVB</button>
                </form>

                <form action="{{ route('documentacion.seccion', ['seccion' => 'fmvb']) }}" method="GET">
                    <button type="submit" class="doc-button no-select">FMVB</button>
                </form>
            </div>
        </div>
    </div>
@endsection
