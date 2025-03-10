<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered; // <- Importar evento

class AuthController extends Controller
{
    /**
     * Registra un nuevo usuario en la base de datos.
     * 
     * Se validan los datos del usuario antes de su creación. Se genera un 
     * token de autenticación utilizando Sanctum.
     *
     * @param Request $request La solicitud HTTP con los datos del usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta con los datos del usuario registrado y su token de acceso.
     */
    public function register(Request $request)
    {
        // Validación de los datos de entrada
        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Crear el usuario en la base de datos
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Rol predeterminado
        ]);

        // 🔹 Disparar el evento para que Laravel envíe el email de verificación
        event(new Registered($user));

        // Generar un token de acceso para el usuario registrado.
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado con éxito. Se ha enviado un email de verificación.',
            'user' => $user->only(['id', 'name', 'email', 'role', 'created_at']),
            'token' => $token,
        ], 201);
    }

    /**
     * Autentica a un usuario y genera un token de acceso.
     * 
     * Se valida la existencia del usuario en la base de datos y se verifica 
     * la contraseña proporcionada. Si la autenticación es correcta, se genera 
     * un token de acceso.
     *
     * @param Request $request La solicitud HTTP con credenciales de usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta con los datos del usuario autenticado y su token de acceso.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        // Se genera un token con Sanctum para el usuario autenticado.
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * Cierra la sesión del usuario autenticado eliminando sus tokens activos.
     * 
     * Se eliminan todos los tokens asociados al usuario, invalidando cualquier 
     * sesión activa en la aplicación.
     *
     * @param Request $request La solicitud HTTP con la autenticación del usuario.
     * @return \Illuminate\Http\JsonResponse Respuesta confirmando el cierre de sesión.
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada'], 204);
    }

    public function webLogin(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dashboard')->with('success', 'Inicio de sesión exitoso.');
        }

        return back()->withErrors(['name' => 'Las credenciales no son correctas.']);
    }
}
