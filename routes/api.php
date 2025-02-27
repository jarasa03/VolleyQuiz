<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnswersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DifficultyScoreController;

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
