<?php

namespace App\Http\Controllers;

use App\Models\DifficultyScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DifficultyScoreController extends Controller
{
    // Obtener todas las puntuaciones
    public function index()
    {
        return response()->json(DifficultyScore::all(), 200);
    }

    // Crear una nueva puntuación
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'test_id' => 'required|exists:tests,id',
            'difficulty' => 'required|string|in:bajo,medio,alto',
            'score' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verificamos si ya existe una puntuación para ese usuario, test y dificultad
        $existingScore = DifficultyScore::where('user_id', $request->user_id)
            ->where('test_id', $request->test_id)
            ->where('difficulty', $request->difficulty)
            ->first();

        if ($existingScore) {
            // Si la nueva puntuación es mayor, la actualizamos
            if ($request->score > $existingScore->score) {
                $existingScore->update(['score' => $request->score]);
                return response()->json(['message' => 'Puntuación actualizada', 'score' => $existingScore], 200);
            } else {
                return response()->json(['message' => 'La puntuación es menor o igual a la actual y no se actualiza'], 200);
            }
        }

        // Si no existe, creamos una nueva
        $difficultyScore = DifficultyScore::create($request->all());

        return response()->json($difficultyScore, 201);
    }

    // Obtener una puntuación específica por ID
    public function show($id)
    {
        $difficultyScore = DifficultyScore::find($id);

        if (!$difficultyScore) {
            return response()->json(['message' => 'Puntuación no encontrada'], 404);
        }

        return response()->json($difficultyScore, 200);
    }

    // Actualizar una puntuación (Solo si la nueva es mayor)
    public function update(Request $request, $id)
    {
        $difficultyScore = DifficultyScore::find($id);

        if (!$difficultyScore) {
            return response()->json(['message' => 'Puntuación no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'score' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->score > $difficultyScore->score) {
            $difficultyScore->update(['score' => $request->score]);
            return response()->json(['message' => 'Puntuación actualizada', 'score' => $difficultyScore], 200);
        }

        return response()->json(['message' => 'La nueva puntuación es menor o igual a la actual y no se actualiza'], 200);
    }

    // Eliminar una puntuación
    public function destroy($id)
    {
        $difficultyScore = DifficultyScore::find($id);

        if (!$difficultyScore) {
            return response()->json(['message' => 'Puntuación no encontrada'], 404);
        }

        $difficultyScore->delete();

        return response()->json(['message' => 'Puntuación eliminada'], 200);
    }
}
