@extends('layouts.app')

@section('title', 'Modo Zen - Pregunta ' . ($index + 1))

@push('body-class')
    zen-page
@endpush

@section('content')
    <div class="zen-container">
        <div class="zen-header">
            <h2>Pregunta {{ $index + 1 }} de 10</h2>
            <p>{{ $question->question_text }}</p>
        </div>

        <form action="{{ route('zen.answer', ['index' => $index]) }}" method="POST" class="zen-form">
            @csrf

            <div class="zen-options">
                @if ($question->question_type === 'multiple_choice')
                    @foreach ($question->answers as $respuesta)
                        <label class="zen-option">
                            <input type="checkbox" name="respuesta_id[]" value="{{ $respuesta->id }}">
                            <span>{{ $respuesta->answer_text }}</span>
                        </label>
                    @endforeach
                @else
                    @foreach ($question->answers as $respuesta)
                        <label class="zen-option">
                            <input type="radio" name="respuesta_id" value="{{ $respuesta->id }}" required>
                            <span>
                                @if ($question->question_type === 'true_false')
                                    {{ $respuesta->answer_text === 'true' ? 'Verdadero' : 'Falso' }}
                                @else
                                    {{ $respuesta->answer_text }}
                                @endif
                            </span>
                        </label>
                    @endforeach
                @endif
            </div>

            <button type="submit" class="zen-button">Responder</button>
        </form>
    </div>
@endsection
