<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

trait AuthHelpers
{
    /**
     * Obtiene el usuario autenticado de la solicitud.
     */
    public function getAuthenticatedUser()
    {
        $authUser = Auth::user();

        if (!$authUser) {
            $token = request()->bearerToken();
            if ($token) {
                $personalAccessToken = PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $authUser = $personalAccessToken->tokenable;
                    Auth::setUser($authUser);
                }
            }
        }

        if (!$authUser) {
            return redirect()->route('auth.login')->with('error', '❌ Debes iniciar sesión para acceder a esta sección.');
        }

        return $authUser;
    }
}
