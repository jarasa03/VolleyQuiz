<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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

// Ruta para manejar la verificación del email y redirigir al dashboard
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request, $id, $hash) {
    $user = \App\Models\User::find($id);

    if (!$user) {
        return redirect()->route('auth.login')->with('error', '❌ Usuario no encontrado.');
    }

    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return redirect()->route('auth.login')->with('error', '❌ Enlace de verificación no válido.');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('auth.login')->with('warning', '⚠️ El email ya ha sido verificado.');
    }

    // Marcar como verificado
    $user->markEmailAsVerified();

    return redirect()->route('auth.login')->with('message', '✅ ¡Email verificado con éxito! Ahora puedes iniciar sesión.');
})->middleware(['signed'])->name('verification.verify');
