<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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

        // Validación de los datos de entrada con mensajes personalizados
        $request->validate([
            'name' => 'required|string|min:3|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ], [
            'name.required' => '❌ El nombre de usuario es obligatorio.',
            'name.min' => '⚠️ El nombre de usuario debe tener al menos 3 caracteres.',
            'name.max' => '⚠️ El nombre de usuario no puede tener más de 20 caracteres.',
            'name.unique' => '❌ Este nombre de usuario ya está en uso.',

            'email.required' => '❌ El correo electrónico es obligatorio.',
            'email.email' => '⚠️ Ingresa un correo válido.',
            'email.max' => '⚠️ El correo no puede tener más de 255 caracteres.',
            'email.unique' => '❌ Este correo ya está registrado.',

            'password.required' => '❌ La contraseña es obligatoria.',
            'password.min' => '⚠️ La contraseña debe tener al menos 6 caracteres.',
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
        Auth::guard('web')->logout(); // Asegura que se usa el guard "web"
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('message', 'Sesión cerrada correctamente.');
    }

    /**
     * Muestra la vista de inicio de sesión.
     *
     * Este método se encarga de devolver la vista del formulario de login.
     * No requiere parámetros ni lógica adicional.
     *
     * @return \Illuminate\View\View Vista de la página de inicio de sesión.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Maneja el proceso de autenticación del usuario.
     *
     * Este método valida las credenciales ingresadas por el usuario y,
     * si son correctas, inicia sesión y redirige al dashboard. En caso
     * de que las credenciales sean incorrectas, retorna a la vista de
     * login con un mensaje de error.
     *
     * @param \Illuminate\Http\Request $request La solicitud HTTP con los datos de login.
     * @return \Illuminate\Http\RedirectResponse Redirección al dashboard si la autenticación es exitosa,
     *                                          o de vuelta al login con un mensaje de error si falla.
     */
    public function webLogin(Request $request)
    {
        // Validar que el usuario haya proporcionado un nombre y una contraseña válidos.
        $credentials = $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ], [
            'name.required' => '❌ El nombre de usuario es obligatorio.',
            'password.required' => '❌ La contraseña es obligatoria.',
        ]);

        // Intentar autenticar al usuario con las credenciales proporcionadas.
        if (!Auth::guard('web')->attempt($credentials)) {
            return back()->with('error', '❌ Usuario o contraseña incorrectos.');
        }

        // Regenerar la sesión para proteger contra ataques de fijación de sesión.
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', '✅ Inicio de sesión exitoso.');
    }


    /**
     * Muestra el formulario de registro.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Maneja el registro de un nuevo usuario en la web.
     *
     * Se encarga de validar y enviar los datos a la API de registro para
     * crear un usuario, en lugar de duplicar la lógica aquí.
     */
    public function webRegister(Request $request)
    {
        // Validar los datos antes de enviarlos a la API
        $request->validate([
            'name' => 'required|string|min:3|max:20|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Enviar la solicitud a la API de registro
        $apiResponse = app()->make(AuthController::class)->register($request);

        // Comprobar si el registro fue exitoso
        if ($apiResponse->getStatusCode() === 201) {
            $userData = json_decode($apiResponse->getContent(), true);
            Auth::loginUsingId($userData['user']['id']);
            return redirect()->route('dashboard')->with('success', 'Registro exitoso. Bienvenido!');
        }

        // Si falla, mostrar el mensaje de error
        return back()->withErrors(['error' => 'No se pudo registrar el usuario. Inténtalo de nuevo.']);
    }

    /**
     * Muestra la vista del formulario de solicitud de recuperación de contraseña.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Maneja el envío del enlace de recuperación de contraseña al email del usuario.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => '❌ El correo electrónico es obligatorio.',
            'email.email' => '⚠️ Ingresa un correo válido.',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('message', '📩 Se ha enviado un enlace a tu correo.')
            : back()->with('error', '❌ No se pudo enviar el enlace de recuperación.');
    }


    /**
     * Muestra el formulario de restablecimiento de contraseña.
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Maneja la actualización de la contraseña después de recibir el enlace de recuperación.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required',
        ], [
            'email.required' => '❌ El correo electrónico es obligatorio.',
            'email.email' => '⚠️ Ingresa un correo válido.',
            'password.required' => '❌ La nueva contraseña es obligatoria.',
            'password.min' => '⚠️ La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => '❌ Las contraseñas no coinciden.',
            'token.required' => '❌ El enlace de restablecimiento no es válido.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth.login')->with('message', '✅ Tu contraseña ha sido restablecida.')
            : back()->with('error', '❌ No se pudo restablecer la contraseña.');
    }
}
