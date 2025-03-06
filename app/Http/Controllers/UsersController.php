<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para la gestión de usuarios.
 */
class UsersController extends Controller
{
    /**
     * Obtiene la lista de todos los usuarios.
     * 
     * Solo un usuario con rol de administrador puede acceder a esta información.
     *
     * @return \Illuminate\Http\JsonResponse Respuesta con la lista de usuarios o un mensaje de error.
     */
    public function index()
    {
        // Asegurar que el usuario se autentique manualmente si Auth::user() es null
        $user = Auth::user();

        if (!$user) {
            $token = request()->bearerToken(); // Obtener el token de la cabecera
            if ($token) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $user = $personalAccessToken->tokenable;
                    Auth::setUser($user); // Forzar la autenticación manualmente
                }
            }
        }

        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if (!method_exists($user, 'isAdmin')) {
            return response()->json(['message' => 'El método isAdmin() no está definido en el modelo User'], 500);
        }

        if (!$user->isAdmin()) { // Bloquear si no es admin
            return response()->json(['message' => 'Acceso denegado - No eres admin'], 403);
        }

        return response()->json(User::all(), 200);
    }

    /**
     * Obtiene la información de un usuario por su ID.
     * 
     * Un usuario solo puede ver su propio perfil, mientras que un administrador puede ver cualquier perfil.
     *
     * @param int $id ID del usuario a consultar.
     * @return \Illuminate\Http\JsonResponse Respuesta con la información del usuario o un mensaje de error.
     */
    public function show($id)
    {
        // Asegurar que el usuario se autentique manualmente si Auth::user() es null
        $authUser = Auth::user();

        if (!$authUser) {
            $token = request()->bearerToken(); // Obtener el token de la cabecera
            if ($token) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $authUser = $personalAccessToken->tokenable;
                    Auth::setUser($authUser); // Forzar la autenticación manualmente
                }
            }
        }

        if (!$authUser) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Verificar que el método isAdmin existe en el modelo
        if (!method_exists($authUser, 'isAdmin')) {
            return response()->json(['message' => 'El método isAdmin() no está definido en el modelo User'], 500);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        if ($authUser->id !== $user->id && !$authUser->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        return response()->json($user, 200);
    }


    /**
     * Crea un nuevo usuario en la base de datos.
     * 
     * Solo un administrador puede registrar nuevos usuarios y asignarles roles específicos.
     *
     * @param Request $request Datos enviados en la solicitud.
     * @return \Illuminate\Http\JsonResponse Respuesta con los datos del usuario creado o un mensaje de error.
     */
    public function store(Request $request)
    {
        // Asegurar que el usuario se autentique manualmente si Auth::user() es null
        $authUser = Auth::user();

        if (!$authUser) {
            $token = request()->bearerToken(); // Obtener el token de la cabecera
            if ($token) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $authUser = $personalAccessToken->tokenable;
                    Auth::setUser($authUser); // Forzar la autenticación manualmente
                }
            }
        }

        if (!$authUser) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Verificar que el método isAdmin existe en el modelo
        if (!method_exists($authUser, 'isAdmin')) {
            return response()->json(['message' => 'El método isAdmin() no está definido en el modelo User'], 500);
        }

        if (!$authUser->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado - No eres admin'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|string|in:user,admin', // Solo admin puede establecer "admin"
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = $request->has('role') ? $request->role : 'user';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
        ]);

        return response()->json($user, 201);
    }


    /**
     * Actualiza los datos de un usuario existente.
     * 
     * Un usuario puede actualizar su propio perfil, mientras que un administrador puede modificar cualquier usuario.
     *
     * @param Request $request Datos enviados en la solicitud.
     * @param int $id ID del usuario a actualizar.
     * @return \Illuminate\Http\JsonResponse Respuesta con los datos actualizados o un mensaje de error.
     */
    public function update(Request $request, $id)
    {
        // Asegurar que el usuario se autentique manualmente si Auth::user() es null
        $authUser = Auth::user();

        if (!$authUser) {
            $token = request()->bearerToken(); // Obtener el token de la cabecera
            if ($token) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $authUser = $personalAccessToken->tokenable;
                    Auth::setUser($authUser); // Forzar la autenticación manualmente
                }
            }
        }

        if (!$authUser) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Verificar que el método isAdmin existe en el modelo
        if (!method_exists($authUser, 'isAdmin')) {
            return response()->json(['message' => 'El método isAdmin() no está definido en el modelo User'], 500);
        }

        // Solo el usuario autenticado o un admin pueden actualizar
        if ($authUser->id !== $user->id && !$authUser->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|string|in:user,admin', // Solo admin puede cambiar roles
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Solo los admins pueden cambiar roles
        if ($authUser->isAdmin() && $request->has('role')) {
            $user->role = $request->role;
        }

        // Actualizamos los otros campos si se proporcionan
        $user->update($request->only(['name', 'email']));

        // Si hay nueva contraseña, la encriptamos antes de guardarla
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json($user, 200);
    }


    /**
     * Elimina un usuario de la base de datos.
     * 
     * Solo un administrador puede eliminar usuarios. Ningún usuario puede eliminar su propia cuenta.
     *
     * @param int $id ID del usuario a eliminar.
     * @return \Illuminate\Http\JsonResponse Respuesta con un mensaje de confirmación o error.
     */
    public function destroy($id)
    {
        // Asegurar que el usuario se autentique manualmente si Auth::user() es null
        $authUser = Auth::user();

        if (!$authUser) {
            $token = request()->bearerToken(); // Obtener el token de la cabecera
            if ($token) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $authUser = $personalAccessToken->tokenable;
                    Auth::setUser($authUser); // Forzar la autenticación manualmente
                }
            }
        }

        if (!$authUser) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Verificar que el método isAdmin existe en el modelo
        if (!method_exists($authUser, 'isAdmin')) {
            return response()->json(['message' => 'El método isAdmin() no está definido en el modelo User'], 500);
        }

        // Solo los administradores pueden eliminar usuarios
        if (!$authUser->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado - No eres admin'], 403);
        }

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // Prevenir que un usuario se elimine a sí mismo
        if ($authUser->id === $user->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado'], 200);
    }
}
