<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnswersController;
use App\Http\Controllers\CategoriesController;

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
