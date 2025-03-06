<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AnswersController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DifficultyScoreController;
use App\Http\Controllers\DocumentsController;
use App\Http\Controllers\DocumentSectionsController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\TagsController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestAttemptsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;

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

Route::apiResource('tests', TestController::class)->names([
    'index'   => 'tests.index',   // Obtener todos los tests
    'store'   => 'tests.store',   // Crear un nuevo test
    'show'    => 'tests.show',    // Obtener un test específico
    'update'  => 'tests.update',  // Actualizar un test
    'destroy' => 'tests.destroy', // Eliminar un test
]);

Route::apiResource('test-attempts', TestAttemptsController::class)->names([
    'index'   => 'test-attempts.index',   // Obtener todos los intentos
    'store'   => 'test-attempts.store',   // Crear un intento
    'show'    => 'test-attempts.show',    // Obtener un intento
    'update'  => 'test-attempts.update',  // Actualizar un intento
    'destroy' => 'test-attempts.destroy', // Eliminar un intento
]);

// Rutas protegidas por Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UsersController::class, 'show'])->name('users.show');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');
});

// Autenticación
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});

// Esto soluciona el error "Route [login] not defined."
Route::get('/login', function () {
    return response()->json(['message' => 'Debes iniciar sesión'], 401);
})->name('login');

// Ruta para reenviar el correo de verificación
Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'El email ya está verificado.'], 200);
    }

    $request->user()->sendEmailVerificationNotification();
    return response()->json(['message' => 'Email de verificación enviado.'], 200);
})->middleware(['auth:sanctum']);

// Ruta para verificar el email
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::find($id);

    if (!$user) {
        return view('auth.login', ['message' => '❌ Usuario no encontrado.']);
    }

    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return view('auth.login', ['message' => '⚠️ Enlace de verificación no válido.']);
    }

    if ($user->hasVerifiedEmail()) {
        return view('auth.login', ['message' => 'ℹ️ El email ya ha sido verificado.']);
    }

    $user->markEmailAsVerified();

    return view('auth.login', ['message' => '✅ ¡Email verificado con éxito! Ahora puedes iniciar sesión.']);
})->name('verification.verify');
