<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\TagsController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/send-test-email', function () {
    $details = [
        'title' => 'Correo de Prueba',
        'body' => 'Este es un correo de prueba enviado desde Laravel usando Gmail SMTP.'
    ];

    Mail::raw($details['body'], function ($message) {
        $message->to('berta-arrua@hotmail.com') // Cambia esto por un correo de prueba real
            ->subject('Correo de Prueba Laravel');
    });

    return "Correo enviado correctamente!";
});

Route::get('/login', function () {
    return view('auth.login');
})->name('auth.login');

Route::post('/login', [AuthController::class, 'webLogin'])->name('auth.login.post');

// Ruta para mostrar el login
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');

// Ruta para procesar el login
Route::post('/login', [AuthController::class, 'webLogin'])->name('auth.login.post');

// Rutas protegidas manualmente
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('auth.login')->with('error', '❌ Debes iniciar sesión antes de acceder.');
    }
    return view('dashboard');
})->name('dashboard');

// Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register');
Route::post('/register', [AuthController::class, 'webRegister'])->name('auth.register.post');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    // Buscar el usuario en la base de datos
    $user = User::find($id);

    // Si el usuario no existe, mostrar un mensaje claro
    if (!$user) {
        return redirect()->route('auth.login')->with('error', '❌ Enlace de verificación inválido o caducado.');
    }

    // Validar que el hash del enlace coincida con el email del usuario
    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return redirect()->route('auth.login')->with('error', '❌ Enlace de verificación no válido.');
    }

    // Si el usuario ya ha verificado su email, redirigir con advertencia
    if ($user->email_verified_at !== null) { // ⚠️ Evita usar hasVerifiedEmail() si no está en sesión
        return redirect()->route('auth.login')->with('warning', '⚠️ Tu correo ya estaba verificado.');
    }

    // Verificar el email y guardar los cambios
    $user->markEmailAsVerified();

    return redirect()->route('auth.login')->with('message', '✅ ¡Email verificado con éxito! Ahora puedes iniciar sesión.');
})->middleware(['signed'])->name('verification.verify');

Route::get('/password/reset', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/password/reset/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/perfil', [UsersController::class, 'verPerfil'])->name('users.perfil');

// Rutas de administración protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
});

// Agrupamos las rutas de administración bajo el prefijo "admin" y protegemos con autenticación
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Solo superadmin puede gestionar usuarios
    Route::middleware(['superadmin'])->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
    });

    // Administración de preguntas y tags (accesible para admin y superadmin)
    Route::get('/questions', [QuestionsController::class, 'index'])->name('admin.questions');
    Route::get('/tags', [TagsController::class, 'index'])->name('admin.tags');
});
// Rutas protegidas manualmente para administración
Route::prefix('admin')->middleware('auth')->group(function () {
    // Ruta para el dashboard
    Route::get('/dashboard', function () {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', '❌ Debes iniciar sesión antes de acceder.');
        }
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Ruta para obtener los usuarios
    Route::get('/users', [UsersController::class, 'index'])->name('admin.users.index'); // Cambié la función a la del controlador

    // Usar el controlador para editar un usuario
    Route::get('/users/{id}/edit', [UsersController::class, 'edit'])->name('admin.users.edit'); // Cambié a la función del controlador

    // Ruta para crear un nuevo usuario
    Route::get('/users/create', [UsersController::class, 'create'])->name('admin.users.create'); // Nueva ruta para mostrar el formulario de creación

    // Ruta para almacenar el nuevo usuario
    Route::post('/users', [UsersController::class, 'store'])->name('admin.users.store'); // Nueva ruta para crear el usuario

    // Ruta para actualizar un usuario
    Route::put('/users/{id}', function ($id) {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', '❌ Debes iniciar sesión antes de acceder.');
        }
        // Aquí llamarías al controlador para actualizar el usuario
        return app(UsersController::class)->update(request(), $id);
    })->name('admin.users.update');

    // Ruta para eliminar un usuario
    Route::delete('/users/{id}', function ($id) {
        if (!Auth::check()) {
            return redirect()->route('auth.login')->with('error', '❌ Debes iniciar sesión antes de acceder.');
        }
        // Aquí llamarías al controlador para eliminar el usuario
        return app(UsersController::class)->destroy($id);
    })->name('admin.users.delete');

    // 📌 Gestión de Tags (solo administradores)
    Route::prefix('tags')->middleware('auth')->group(function () {
        Route::get('/', [TagsController::class, 'index'])->name('admin.tags.index');
        Route::get('/create', [TagsController::class, 'create'])->name('admin.tags.create');
        Route::post('/', [TagsController::class, 'store'])->name('admin.tags.store');
        Route::get('/{id}/edit', [TagsController::class, 'edit'])->name('admin.tags.edit');
        Route::put('/{id}', [TagsController::class, 'update'])->name('admin.tags.update');
        Route::delete('/{id}', [TagsController::class, 'destroy'])->name('admin.tags.delete');
    });
    Route::post('/admin/tags', [TagsController::class, 'store'])->name('admin.tags.store');
});
