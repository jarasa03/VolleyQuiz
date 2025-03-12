<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

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
