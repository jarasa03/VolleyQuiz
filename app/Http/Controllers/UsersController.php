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

        // Cambié 'User::paginate(10)' por 'User::orderBy('name')->paginate(10)' para tener un orden claro en la lista
        $users = User::orderBy('name')->paginate(10);

        // Devolver la vista 'admin.users' con la variable 'users'
        return view('admin.users', compact('users'));
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
     * Crea un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        $authUser = $this->getAuthenticatedUser();

        // Verificar si el usuario tiene permisos para agregar un nuevo usuario (solo admins)
        if (!$authUser->isAdmin()) {
            return redirect()->route('admin.users')->with('error', '❌ No tienes permisos para agregar un nuevo usuario.');
        }

        // Validar los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:user,admin,superadmin',
        ]);

        // Crear el nuevo usuario
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('admin.users')->with('message', '✅ Usuario creado con éxito.');
    }

    /**
     * Actualiza los datos de un usuario existente.
     */
    public function update(Request $request, $id)
    {
        // Obtener el usuario autenticado
        $authUser = $this->getAuthenticatedUser();

        // Buscar el usuario a actualizar
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users')->with('error', '❌ Usuario no encontrado');
        }

        // Verificar si el usuario autenticado puede editar este usuario
        if ($authUser->id !== $user->id && !$authUser->isAdmin()) {
            return redirect()->route('admin.users')->with('error', '❌ Acceso denegado');
        }

        // Validación de los datos del formulario
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|string|in:user,admin,superadmin',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.users.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        // Actualizar el rol si el usuario es Superadmin y si se pasó un rol en la solicitud
        if ($authUser->isSuperAdmin() && $request->has('role')) {
            $user->role = $request->role;
        }

        // Actualizar los campos del usuario
        $user->update($request->only(['name', 'email']));

        // Si se pasa una nueva contraseña, se actualiza también
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Redirigir al listado de usuarios con un mensaje de éxito
        return redirect()->route('admin.users')->with('message', '✅ Usuario actualizado correctamente');
    }


    /**
     * Elimina un usuario de la base de datos.
     */
    public function destroy($id)
    {
        $authUser = $this->getAuthenticatedUser();
        $user = User::find($id);

        if (!$user) {
            return redirect()->route('admin.users')->with('error', '❌ Usuario no encontrado');
        }

        if ($authUser->id === $user->id) {
            return redirect()->route('admin.users')->with('error', '❌ No puedes eliminar tu propia cuenta');
        }

        if ($user->isSuperAdmin()) {
            return redirect()->route('admin.users')->with('error', '❌ No puedes eliminar a un superadmin');
        }

        if (!$authUser->isSuperAdmin() && $user->isAdmin()) {
            return redirect()->route('admin.users')->with('error', '❌ No puedes eliminar un administrador');
        }

        $user->delete();
        return redirect()->route('admin.users')->with('message', '✅ Usuario eliminado');
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

    public function edit($id)
    {
        $user = User::findOrFail($id); // Obtiene el usuario por su ID

        return view('admin.edit-user', compact('user')); // Pasa el usuario a la vista
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        return view('admin.create-user'); // Vista donde se muestra el formulario
    }
}
