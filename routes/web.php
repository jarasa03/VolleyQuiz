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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Puedes agregar más vistas protegidas aquí
});

Route::post('/logout', function () {
    Auth::guard('web')->logout(); // Asegurar que el logout se hace en el guard correcto
    request()->session()->invalidate(); // Invalida la sesión
    request()->session()->regenerateToken(); // Regenera el token CSRF

    return redirect('/login')->with('message', 'Sesión cerrada correctamente.');
})->name('auth.logout');
