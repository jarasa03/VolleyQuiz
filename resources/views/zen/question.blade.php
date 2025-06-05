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

            {{-- Mostrar etiquetas con sus colores --}}
            <div class="zen-tags" style="margin-top: 10px;">
                @foreach ($question->tags as $tag)
                    <span
                        style="
                        color: {{ $tag->color }};
                        border: 2px solid {{ $tag->color }};
                        background-color: {{ $tag->color }}20;
                        padding: 5px 10px;
                        border-radius: 20px;
                        font-weight: bold;
                        display: inline-block;
                        margin: 2px 5px 5px 0;
                    ">
                        {{ $tag->name }}
                    </span>
                @endforeach
            </div>
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
