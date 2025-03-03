<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestAttemptsController extends Controller
{
    // Obtener todos los intentos
    public function index()
    {
        return response()->json(TestAttempt::all(), 200);
    }

    // Crear un nuevo intento de test
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id', // Debe ser un usuario válido
            'test_id' => 'required|exists:tests,id', // Debe ser un test válido
            'score' => 'required|integer|min:0',
            'time_taken' => 'required|integer|min:0',
            'streak' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $testAttempt = TestAttempt::create([
            'user_id' => $request->user_id,
            'test_id' => $request->test_id,
            'score' => $request->score,
            'time_taken' => $request->time_taken,
            'streak' => $request->streak,
        ]);

        return response()->json($testAttempt, 201);
    }

    // Obtener un intento por ID
    public function show($id)
    {
        $testAttempt = TestAttempt::find($id);

        if (!$testAttempt) {
            return response()->json(['message' => 'Intento no encontrado'], 404);
        }

        return response()->json($testAttempt, 200);
    }

    // Actualizar un intento de test
    public function update(Request $request, $id)
    {
        $testAttempt = TestAttempt::find($id);

        if (!$testAttempt) {
            return response()->json(['message' => 'Intento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'score' => 'sometimes|integer|min:0',
            'time_taken' => 'sometimes|integer|min:0',
            'streak' => 'sometimes|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $testAttempt->update($request->only(['score', 'time_taken', 'streak']));

        return response()->json($testAttempt, 200);
    }

    // Eliminar un intento de test
    public function destroy($id)
    {
        $testAttempt = TestAttempt::find($id);

        if (!$testAttempt) {
            return response()->json(['message' => 'Intento no encontrado'], 404);
        }

        $testAttempt->delete();

        return response()->json(['message' => 'Intento eliminado'], 200);
    }
}
