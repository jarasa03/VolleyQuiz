<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

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
        return redirect()->route('auth.login')->with('error', 'Debes iniciar sesión antes de acceder.');
    }
    return view('dashboard');
})->name('dashboard');

// Ruta para cerrar sesión
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');