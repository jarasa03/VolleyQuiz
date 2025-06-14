@extends('layouts.app')

@section('title', 'Modo Zen')

@push('body-class', 'zen-page')

@section('content')
    <div class="zen-container">
        <div class="zen-header">
            <h1>Modo Zen</h1>
            <p>Responde 10 preguntas aleatorias, sin presión. Verás la explicación tras cada respuesta.</p>
        </div>

        <form action="{{ route('zen.start') }}" method="POST">
            @csrf
            <button type="submit" class="zen-button">
                Comenzar
            </button>
        </form>
    </div>
@endsection
