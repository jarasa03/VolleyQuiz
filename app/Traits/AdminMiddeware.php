<?php

namespace App\Traits;

trait AdminMiddleware
{
    use AuthHelpers; // 🔹 Usamos el otro Trait para obtener el usuario autenticado

    /**
     * Aplica el middleware de autenticación y autorización en el constructor.
     */
    public function applyAdminMiddleware()
    {
        $this->middleware(function ($request, $next) {
            $user = $this->getAuthenticatedUser(); // 🔹 Llamamos al método del Trait

            if (!$user) {
                return redirect()->route('auth.login')
                    ->with('error', '❌ Debes iniciar sesión para acceder.');
            }

            if (!$user->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('error', '❌ No tienes permisos para acceder a esta sección.');
            }

            return $next($request);
        });
    }
}
