<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use App\Traits\AuthHelpers;
use App\Traits\AdminMiddleware;

/**
 * Controlador para la gestión de usuarios.
 */
class UsersController extends Controller
{
    use AuthHelpers, AdminMiddleware;

    public function __construct()
    {
        $this->applyAdminMiddleware(); // 🔹 Llamamos al método del Trait en lugar de definir el constructor manualmente
    }


    /**
     * Obtiene la lista de todos los usuarios.
     */
    public function index(Request $request)
    {
        // Iniciar la consulta de usuarios
        $query = User::query();

        // Filtrar por nombre o email si se ha introducido un término de búsqueda
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        }

        // Ordenar por ID ascendente y paginar
        $users = $query->orderBy('id', 'asc')->paginate(10);

        // Devolver la vista con los usuarios filtrados si aplica
        return view('admin.users.index', compact('users'));
    }


    /**
     * Obtiene la información de un usuario por su ID.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user, 200);
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:user,admin,superadmin',
        ]);

        // Crear el nuevo usuario
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
        ]);

        return redirect()->route('admin.users.index')->with('message', '✅ Usuario creado con éxito.');
    }

    /**
     * Actualiza los datos de un usuario existente.
     */
    public function update(Request $request, $id)
    {

        // Buscar el usuario a actualizar
        $user = User::findOrFail($id);

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

        // Actualizar los campos del usuario
        $user->update($request->only(['name', 'email']));

        // Si se pasa una nueva contraseña, se actualiza también
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Redirigir al listado de usuarios con un mensaje de éxito
        return redirect()->route('admin.users.index')->with('message', '✅ Usuario actualizado correctamente');
    }


    public function destroy($id)
    {
        $authUser = $this->getAuthenticatedUser();
        $user = User::findOrFail($id);

        // No permitir que se elimine a sí mismo
        if ($authUser->id === $user->id) {
            return redirect()->route('admin.users.index')->with('error', '❌ No puedes eliminar tu propia cuenta');
        }

        // Permitir que un superadmin elimine a otro superadmin
        // Si no es un superadmin, se evitará eliminar a otro superadmin
        if ($authUser->isSuperAdmin() && !$user->isSuperAdmin()) {
            $user->delete();
            return redirect()->route('admin.users.index')->with('message', '✅ Usuario eliminado');
        }

        // Restringir eliminación si el usuario autenticado no es superadmin
        if (!$authUser->isSuperAdmin() && $user->isAdmin()) {
            return redirect()->route('admin.users.index')->with('error', '❌ No puedes eliminar un administrador');
        }

        // Si todo está bien, eliminar al usuario
        $user->delete();
        return redirect()->route('admin.users.index')->with('message', '✅ Usuario eliminado');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $authUser = $this->getAuthenticatedUser(); // 🔹 Solo si necesitas datos del usuario autenticado

        return view('admin.users.edit', compact('user', 'authUser'));
    }


    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create()
    {
        return view('admin.users.create'); // Vista donde se muestra el formulario
    }
}
