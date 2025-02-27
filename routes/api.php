<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnswersController;

Route::apiResource('answers', AnswersController::class)->names([
    'index'   => 'answers.index',
    'store'   => 'answers.store',
    'show'    => 'answers.show',
    'update'  => 'answers.update',
    'destroy' => 'answers.destroy',
]);
