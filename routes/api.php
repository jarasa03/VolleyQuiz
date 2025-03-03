<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnswersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DifficultyScoreController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\DocumentSectionsController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\TagsController;

Route::apiResource('answers', AnswersController::class)->names([
    'index'   => 'answers.index', // Obtener todas las respuestas
    'store'   => 'answers.store', // Crear una nueva respuesta
    'show'    => 'answers.show', // Obtener una respuesta por ID
    'update'  => 'answers.update', // Actualizar una respuesta
    'destroy' => 'answers.destroy', // Eliminar una categoría
]);

Route::apiResource('categories', CategoriesController::class)->names([
    'index'   => 'categories.index',   // Obtener todas las categorías
    'store'   => 'categories.store',   // Crear una nueva categoría
    'show'    => 'categories.show',    // Obtener una categoría por ID
    'update'  => 'categories.update',  // Actualizar una categoría
    'destroy' => 'categories.destroy', // Eliminar una categoría
]);

Route::apiResource('difficulty-scores', DifficultyScoreController::class)->names([
    'index'   => 'difficulty-scores.index',   // Obtener todas las puntuaciones
    'store'   => 'difficulty-scores.store',   // Crear una nueva puntuación
    'show'    => 'difficulty-scores.show',    // Obtener una puntuación específica
    'update'  => 'difficulty-scores.update',  // Actualizar una puntuación (si la nueva es mayor)
    'destroy' => 'difficulty-scores.destroy', // Eliminar una puntuación
]);

Route::apiResource('documents', DocumentsController::class)->names([
    'index'   => 'documents.index',    // Obtener todos los documentos
    'store'   => 'documents.store',    // Subir un nuevo documento
    'show'    => 'documents.show',     // Obtener un documento específico
    'update'  => 'documents.update',   // Actualizar un documento
    'destroy' => 'documents.destroy',  // Eliminar un documento
]);

// Ruta específica para descargar documentos
Route::get('documents/{id}/download', [DocumentsController::class, 'download'])->name('documents.download');

Route::apiResource('document-sections', DocumentSectionsController::class)->names([
    'index'   => 'document-sections.index',   // Obtener todas las secciones de documentos
    'store'   => 'document-sections.store',   // Crear una nueva sección
    'show'    => 'document-sections.show',    // Obtener una sección específica
    'update'  => 'document-sections.update',  // Actualizar una sección
    'destroy' => 'document-sections.destroy', // Eliminar una sección
]);

Route::apiResource('questions', QuestionsController::class)->names([
    'index'   => 'questions.index',   // Obtener todas las preguntas
    'store'   => 'questions.store',   // Crear una nueva pregunta
    'show'    => 'questions.show',    // Obtener una pregunta específica
    'update'  => 'questions.update',  // Actualizar una pregunta
    'destroy' => 'questions.destroy', // Eliminar una pregunta
]);

// Rutas específicas para poner o quitar tags de las preguntas
Route::post('questions/{id}/tags', [QuestionsController::class, 'attachTags'])->name('questions.attachTags');
Route::delete('questions/{question_id}/tags/{tag_id}', [QuestionsController::class, 'detachTag'])->name('questions.detachTag');

Route::apiResource('tags', TagsController::class)->names([
    'index'   => 'tags.index',   // Obtener todas las etiquetas
    'store'   => 'tags.store',   // Crear una nueva etiqueta
    'show'    => 'tags.show',    // Obtener una etiqueta específica
    'update'  => 'tags.update',  // Actualizar una etiqueta
    'destroy' => 'tags.destroy', // Eliminar una etiqueta
]);
