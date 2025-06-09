@extends('layouts.app')

@section('title', 'Explicación - Pregunta ' . ($index + 1))

@push('body-class')
    zen-page
@endpush

@section('content')
    @php
        function mostrarTexto($texto, $tipo)
        {
            return $tipo === 'true_false' ? ($texto === 'true' ? 'Verdadero' : 'Falso') : $texto;
        }
    @endphp

    <div class="zen-container">
        <div class="zen-header">
            <h2>Explicación</h2>

            @if ($esCorrecta)
                <p>✅ ¡Correcto!</p>
            @else
                <p>❌ Incorrecto.</p>

                @if ($question->question_type === 'multiple_choice')
                    <div class="zen-feedback">
                        <p><strong>Respuestas seleccionadas:</strong></p>
                        <ul class="zen-list">
                            @foreach ($question->answers as $respuesta)
                                @if (in_array($respuesta->id, $respuestaSeleccionada ?? []))
                                    <li class="{{ $respuesta->is_correct ? 'correct' : 'incorrect' }}">
                                        {{ mostrarTexto($respuesta->answer_text, $question->question_type) }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <p><strong>Respuestas correctas:</strong></p>
                        <ul class="zen-list">
                            @foreach ($question->answers->where('is_correct', true) as $correcta)
                                <li class="correct">
                                    {{ mostrarTexto($correcta->answer_text, $question->question_type) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    <p>La respuesta correcta era: <strong>
                            {{ mostrarTexto($question->answers->firstWhere('is_correct', true)?->answer_text, $question->question_type) }}
                        </strong></p>

                    @if ($question->question_type !== 'true_false')
                        <p>Tu respuesta: <strong>
                                {{ mostrarTexto($question->answers->firstWhere('id', $respuestaSeleccionada)?->answer_text, $question->question_type) }}
                            </strong></p>
                    @endif
                @endif
            @endif
        </div>

        @if ($question->explanation && $question->explanation->text)
            <div class="zen-explanation-text">
                <h3>¿Por qué?</h3>
                <p>{{ $question->explanation->text }}</p>
            </div>
        @endif

        @if ($question->explanation && $question->explanation->image_path)
            <div class="zen-explanation-image">
                <img src="{{ asset($question->explanation->image_path) }}" alt="Explicación visual">
            </div>
        @endif

        <form action="{{ $ultima ? route('zen.result') : route('zen.question', ['index' => $index + 1]) }}" method="GET">
            <button type="submit" class="zen-button">
                {{ $ultima ? 'Ver Resultado Final' : 'Siguiente Pregunta' }}
            </button>
        </form>
    </div>
@endsection
