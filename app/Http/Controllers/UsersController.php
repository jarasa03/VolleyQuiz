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
     */
    public function index()
    {
        $user = $this->getAuthenticatedUser();
        if (!$user->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado - No eres admin'], 403);
        }

        return response()->json(User::all(), 200);
    }

    /**
     * Obtiene la información de un usuario por su ID.
     */
    public function show($id)
    {
        $authUser = $this->getAuthenticatedUser();
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
     * Crea un nuevo usuario.
     */
    public function store(Request $request)
    {
        $authUser = $this->getAuthenticatedUser();
        if (!$authUser->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado - No eres admin'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'sometimes|string|in:user,admin,superadmin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = 'user';
        if ($authUser->isSuperAdmin() && $request->has('role')) {
            $role = $request->role;
        }

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
     */
    public function update(Request $request, $id)
    {
        $authUser = $this->getAuthenticatedUser();
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        if ($authUser->id !== $user->id && !$authUser->isAdmin()) {
            return response()->json(['message' => 'Acceso denegado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|string|in:user,admin,superadmin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($authUser->isSuperAdmin() && $request->has('role')) {
            $user->role = $request->role;
        }

        $user->update($request->only(['name', 'email']));

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json($user, 200);
    }

    /**
     * Elimina un usuario de la base de datos.
     */
    public function destroy($id)
    {
        $authUser = $this->getAuthenticatedUser();
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        if ($authUser->id === $user->id) {
            return response()->json(['message' => 'No puedes eliminar tu propia cuenta'], 403);
        }

        if ($user->isSuperAdmin()) {
            return response()->json(['message' => 'No puedes eliminar a un superadmin. Solo el sistema puede gestionarlos.'], 403);
        }

        if (!$authUser->isSuperAdmin() && $user->isAdmin()) {
            return response()->json(['message' => 'No puedes eliminar un administrador'], 403);
        }

        $user->delete();
        return response()->json(['message' => 'Usuario eliminado'], 200);
    }

    /**
     * Obtiene el usuario autenticado de la solicitud.
     */
    private function getAuthenticatedUser()
    {
        $authUser = Auth::user();

        if (!$authUser) {
            $token = request()->bearerToken();
            if ($token) {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    $authUser = $personalAccessToken->tokenable;
                    Auth::setUser($authUser);
                }
            }
        }

        if (!$authUser) {
            abort(response()->json(['message' => 'No autenticado'], 401));
        }

        return $authUser;
    }
}
