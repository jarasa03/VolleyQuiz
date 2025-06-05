@extends('layouts.app')

@section('title', 'Resultado Modo Zen')

@push('body-class')
    zen-page
@endpush

@section('content')
    <div class="zen-container">
        <div class="zen-header">
            <h2>Resultado Final</h2>
            <p>Has acertado <strong>{{ $aciertos }}/{{ $total }}</strong> preguntas.</p>
        </div>

        <form action="{{ route('zen.start') }}" method="POST">
            @csrf
            <button type="submit" class="zen-button">
                Volver a Intentarlo
            </button>
        </form>
    </div>
@endsection
