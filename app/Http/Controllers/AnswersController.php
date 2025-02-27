<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnswersController extends Controller
{
    // Obtener todas las respuestas
    public function index()
    {
        return response()->json(Answer::all(), 200);
    }

    // Crear una nueva respuesta
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'required|string|max:255',
            'is_correct' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $answer = Answer::create($request->all());
        return response()->json($answer, 201);
    }

    // Obtener una respuesta específica
    public function show($id)
    {
        $answer = Answer::find($id);
        if (!$answer) {
            return response()->json(['message' => 'Respuesta no encontrada'], 404);
        }
        return response()->json($answer, 200);
    }

    // Actualizar una respuesta
    public function update(Request $request, $id)
    {
        $answer = Answer::find($id);
        if (!$answer) {
            return response()->json(['message' => 'Respuesta no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'answer_text' => 'sometimes|string|max:255',
            'is_correct' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $answer->update($request->all());
        return response()->json($answer, 200);
    }

    // Eliminar una respuesta
    public function destroy($id)
    {
        $answer = Answer::find($id);
        if (!$answer) {
            return response()->json(['message' => 'Respuesta no encontrada'], 404);
        }
        $answer->delete();
        return response()->json(['message' => 'Respuesta eliminada'], 200);
    }
}
