@extends('layouts.app')

@section('title', 'Crear Nueva Pregunta')

@push('body-class', 'admin-page')

@section('content')
    <div class="create-user-container" id="create-question-container">
        <h1>Crear Nueva Pregunta</h1>

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

        <!-- Formulario para crear pregunta -->
        <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Texto de la pregunta -->
            <div class="form-group">
                <label for="question_text">Texto de la Pregunta:</label>
                <input type="text" name="question_text" id="question_text" value="{{ old('question_text') }}"
                    class="form-control" required>
            </div>

            <!-- Tipo de pregunta -->
            <div class="form-group">
                <label for="question_type">Tipo de Pregunta:</label>
                <select name="question_type" id="question_type" class="form-control" required>
                    <option value="">Seleccionar tipo</option>
                    <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Opción
                        múltiple</option>
                    <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>Verdadero /
                        Falso</option>
                </select>
            </div>

            <!-- Categoría -->
            <div class="form-group">
                <label for="category_id">Categoría:</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <option value="">Seleccionar categoría</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                            style="color: {{ $tag->color }}; border: 2px solid {{ $tag->color }}; background-color: {{ $tag->color }}20; padding: 5px 10px; border-radius: 20px; font-weight: bold; cursor: pointer; display: inline-block; margin: 5px;">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>

                <div class="selected-tags-container">
                    <label>Tags seleccionados:</label>
                    <div id="selected-tags"></div>
                </div>

                <input type="hidden" name="tags" id="tags-input"
                    value="{{ old('tags') ? implode(',', old('tags')) : '' }}">
            </div>

            <!-- Respuestas dinámicas -->
            <div id="answers-container" class="form-group" style="display: none;">
                <label>Respuestas:</label>
                <div id="answer-list"></div>
                <button type="button" id="add-answer" class="btn submit">Agregar Respuesta</button>
            </div>

            <!-- Verdadero/Falso -->
            <div id="true-false-container" class="form-group" style="display: none;">
                <label>Es verdadera o falsa dicha afirmación?</label>
                <div class="true-false-options">
                    <label class="true-false-label">
                        <input type="radio" name="correct_answer" value="true"> Verdadero
                    </label>
                    <label class="true-false-label">
                        <input type="radio" name="correct_answer" value="false"> Falso
                    </label>
                </div>
            </div>

            <!-- Justificación de la respuesta -->
            <div class="form-group">
                <label for="explanation_text">Justificación (texto):</label>
                <textarea name="explanation_text" id="explanation_text" rows="4" class="form-control">{{ old('explanation_text') }}</textarea>
            </div>

            <!-- Imagen de la justificación -->
            <div class="form-group">
                <label for="explanation_image">Imagen para la justificación (opcional):</label>
                <input type="file" name="explanation_image" id="explanation_image" class="form-control">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn submit">✅ Crear Pregunta</button>
                <a href="{{ route('admin.questions.index') }}" class="btn cancel">⬅ Volver</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionType = document.getElementById('question_type');
            const answersContainer = document.getElementById('answers-container');
            const answerList = document.getElementById('answer-list');
            const addAnswerBtn = document.getElementById('add-answer');
            const trueFalseContainer = document.getElementById('true-false-container');
            const availableTags = document.querySelectorAll('.selectable-tag');
            const selectedTagsContainer = document.getElementById('selected-tags');
            const tagsInput = document.getElementById('tags-input');
            let selectedTags = new Set(JSON.parse(tagsInput.value || '[]'));

            function updateTagsInput() {
                tagsInput.value = JSON.stringify(Array.from(selectedTags));
            }

            availableTags.forEach(tag => {
                tag.addEventListener('click', function() {
                    const tagId = this.getAttribute('data-id');
                    if (selectedTags.has(tagId)) {
                        selectedTags.delete(tagId);
                        document.getElementById(`selected-tag-${tagId}`).remove();
                        this.style.opacity = '1';
                    } else {
                        selectedTags.add(tagId);
                        const clone = this.cloneNode(true);
                        clone.id = `selected-tag-${tagId}`;
                        clone.addEventListener('click', function() {
                            selectedTags.delete(tagId);
                            clone.remove();
                            tag.style.opacity = '1';
                            updateTagsInput();
                        });
                        selectedTagsContainer.appendChild(clone);
                        this.style.opacity = '0.5';
                    }
                    updateTagsInput();
                });
            });

            // ✅ Función para alternar selección de respuesta correcta
            function toggleCorrectAnswer(button) {
                button.classList.toggle('correct');
                if (button.classList.contains('correct')) {
                    button.innerHTML = "✔"; // Se marca como correcta
                } else {
                    button.innerHTML = "✔"; // Se mantiene el icono, pero sin color
                }
            }

            // ✅ Restaurar la funcionalidad del botón de "Agregar Respuesta"
            addAnswerBtn.addEventListener('click', function() {
                const answerDiv = document.createElement('div');
                answerDiv.classList.add('answer-item');

                const index = answerList.children.length;

                answerDiv.innerHTML = `
        <input type="text" name="answers[${index}][text]" class="form-control" placeholder="Escribe una respuesta" required>
        <input type="hidden" name="answers[${index}][correct]" value="0" class="correct-flag">
        <button type="button" class="correct-answer-btn">✔</button>
        <button type="button" class="btn remove-answer">❌</button>
    `;

                answerList.appendChild(answerDiv);

                // Eliminar
                answerDiv.querySelector('.remove-answer').addEventListener('click', function() {
                    answerDiv.remove();
                });

                // Marcar como correcta
                const correctBtn = answerDiv.querySelector('.correct-answer-btn');
                const correctInput = answerDiv.querySelector('.correct-flag');

                correctBtn.addEventListener('click', function() {
                    const isCorrect = correctBtn.classList.toggle('correct');
                    correctInput.value = isCorrect ? '1' : '0';
                });
            });



            // ✅ Hacer que el cambio de tipo de pregunta muestre la opción correcta
            questionType.addEventListener('change', function() {
                if (this.value === 'multiple_choice') {
                    answersContainer.style.display = 'block';
                    trueFalseContainer.style.display = 'none';
                } else if (this.value === 'true_false') {
                    answersContainer.style.display = 'none';
                    trueFalseContainer.style.display = 'block';
                } else {
                    answersContainer.style.display = 'none';
                    trueFalseContainer.style.display = 'none';
                }
            });
        });
    </script>
@endsection
