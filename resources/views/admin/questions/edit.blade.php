@extends('layouts.app')

@section('title', 'Editar Pregunta')

@push('body-class', 'admin-page')

@section('content')
    <div class="edit-user-container">
        <h1>Editar Pregunta</h1>

        <!-- Mostrar mensajes de sesión -->
        @if (session()->has('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        @if (session()->has('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        @if (session()->has('message'))
            <div class="alert alert-info">
                {{ session('message') }}
            </div>
        @endif

        <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Texto de la pregunta -->
            <div class="form-group">
                <label for="question_text">Texto de la Pregunta:</label>
                <input type="text" name="question_text" id="question_text"
                    value="{{ old('question_text', $question->question_text) }}" class="form-control" required>
            </div>

            <!-- Tipo de pregunta (bloqueado para evitar cambios) -->
            <div class="form-group">
                <label for="question_type">Tipo de Pregunta:</label>
                <select name="question_type" id="question_type" class="form-control" readonly
                    style="pointer-events: none; background-color: #eee;">
                    <option value="multiple_choice" {{ $question->question_type == 'multiple_choice' ? 'selected' : '' }}>
                        Opción múltiple
                    </option>
                    <option value="true_false" {{ $question->question_type == 'true_false' ? 'selected' : '' }}>
                        Verdadero / Falso
                    </option>
                </select>
                <small class="text-muted">El tipo de pregunta no se puede modificar.</small>
            </div>

            <!-- Categoría -->
            <div class="form-group">
                <label for="category_id">Categoría:</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $question->category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tags con selección interactiva -->
            <div class="form-group">
                <label>Tags (haz clic para seleccionar):</label>
                <div id="available-tags" class="tags-container">
                    @foreach ($tags as $tag)
                        <span class="tag-label selectable-tag" data-id="{{ $tag->id }}"
                            style="color: {{ $tag->color }}; border: 2px solid {{ $tag->color }}; 
                                background-color: {{ $tag->color }}20; padding: 5px 10px; 
                                border-radius: 20px; font-weight: bold; cursor: pointer; 
                                display: inline-block; margin: 5px;"
                            {{ in_array($tag->id, $question->tags->pluck('id')->toArray()) ? 'data-selected=true' : '' }}>
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>

                <div class="selected-tags-container">
                    <label>Tags seleccionados:</label>
                    <div id="selected-tags">
                        @foreach ($question->tags as $tag)
                            <span class="tag-label selected-tag" data-id="{{ $tag->id }}"
                                style="color: {{ $tag->color }}; border: 2px solid {{ $tag->color }}; 
                                    background-color: {{ $tag->color }}20; padding: 5px 10px; 
                                    border-radius: 20px; font-weight: bold; cursor: pointer; 
                                    display: inline-block; margin: 5px;">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <input type="hidden" name="tags" id="tags-input"
                    value="{{ implode(',', $question->tags->pluck('id')->toArray()) }}">
            </div>

            <!-- Respuestas (solo si es opción múltiple) -->
            <div id="answers-container" class="form-group"
                style="{{ $question->question_type == 'multiple_choice' ? 'display: block;' : 'display: none;' }}">
                <label>Respuestas:</label>
                <div id="answer-list">
                    @foreach ($question->answers as $answer)
                        <div class="answer-item">
                            <input type="text" name="answers[]" class="form-control" value="{{ $answer->answer_text }}"
                                required>
                            <button type="button" class="correct-answer-btn {{ $answer->is_correct ? 'correct' : '' }}">
                                ✔
                            </button>
                            <button type="button" class="btn remove-answer">❌</button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-answer" class="btn submit">Agregar Respuesta</button>
            </div>

            <!-- Verdadero/Falso (solo si es de ese tipo) -->
            <div id="true-false-container" class="form-group"
                style="{{ $question->question_type == 'true_false' ? 'display: block;' : 'display: none;' }}">
                <label>Es verdadera o falsa dicha afirmación?</label>

                @php
                    $correctAnswer = $question->answers->where('is_correct', true)->first();
                @endphp

                <div class="true-false-options">
                    <label
                        class="true-false-label {{ $correctAnswer && $correctAnswer->answer_text == 'true' ? 'selected' : '' }}">
                        <input type="radio" name="correct_answer" value="true"
                            {{ $correctAnswer && $correctAnswer->answer_text == 'true' ? 'checked' : '' }}>
                        Verdadero
                    </label>
                    <label
                        class="true-false-label {{ $correctAnswer && $correctAnswer->answer_text == 'false' ? 'selected' : '' }}">
                        <input type="radio" name="correct_answer" value="false"
                            {{ $correctAnswer && $correctAnswer->answer_text == 'false' ? 'checked' : '' }}>
                        Falso
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">Guardar Cambios</button>
                <a href="{{ route('admin.questions.index') }}" class="btn cancel">Cancelar</a>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const addAnswerBtn = document.getElementById('add-answer');
                const answerList = document.getElementById('answer-list');

                function addCorrectToggleLogic(correctBtn, correctInput) {
                    correctBtn.addEventListener('click', function() {
                        const isCorrect = correctBtn.classList.toggle('correct');
                        correctBtn.innerHTML = "✔";
                        correctInput.value = isCorrect ? '1' : '0';
                    });
                }

                // Aplicar lógica a respuestas ya cargadas
                document.querySelectorAll('.answer-item').forEach(item => {
                    const correctBtn = item.querySelector('.correct-answer-btn');
                    const correctInput = document.createElement('input');
                    correctInput.type = 'hidden';
                    correctInput.name = 'correct_answers[]';
                    correctInput.value = correctBtn.classList.contains('correct') ? '1' : '0';
                    item.appendChild(correctInput);
                    addCorrectToggleLogic(correctBtn, correctInput);
                });

                // Nueva respuesta
                addAnswerBtn.addEventListener('click', function() {
                    const answerDiv = document.createElement('div');
                    answerDiv.classList.add('answer-item');
                    answerDiv.innerHTML = `
                        <input type="text" name="answers[]" class="form-control" placeholder="Escribe una respuesta" required>
                        <button type="button" class="correct-answer-btn">✔</button>
                        <button type="button" class="btn remove-answer">❌</button>
                    `;

                    const correctInput = document.createElement('input');
                    correctInput.type = 'hidden';
                    correctInput.name = 'correct_answers[]';
                    correctInput.value = '0';

                    answerDiv.appendChild(correctInput);
                    answerList.appendChild(answerDiv);

                    const correctBtn = answerDiv.querySelector('.correct-answer-btn');
                    const removeBtn = answerDiv.querySelector('.remove-answer');

                    addCorrectToggleLogic(correctBtn, correctInput);

                    removeBtn.addEventListener('click', function() {
                        answerDiv.remove();
                    });
                });
            });

            // Lógica para actualizar visualmente la selección de verdadero/falso
            const trueFalseRadios = document.querySelectorAll('input[name="correct_answer"]');

            trueFalseRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Eliminar clases 'selected' de todos los labels
                    document.querySelectorAll('.true-false-label').forEach(label => {
                        label.classList.remove('selected');
                    });

                    // Añadir clase 'selected' al label seleccionado
                    if (this.checked) {
                        this.closest('.true-false-label').classList.add('selected');
                    }
                });
            });
        </script>



    @endsection
