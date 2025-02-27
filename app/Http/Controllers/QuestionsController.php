<?php

namespace App\Http\Controllers;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionsController extends Controller
{
    // 🔹 Obtener todas las preguntas
    public function index()
    {
        return response()->json(Question::with(['category', 'answers', 'tags'])->get(), 200);
    }

    // 🔹 Crear una nueva pregunta
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:multiple_choice,true_false',
            'category_id'   => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question = Question::create([
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'category_id'   => $request->category_id,
        ]);

        return response()->json($question, 201);
    }

    // 🔹 Obtener una pregunta por ID
    public function show($id)
    {
        $question = Question::with(['category', 'answers', 'tags'])->find($id);

        if (!$question) {
            return response()->json(['message' => 'Pregunta no encontrada'], 404);
        }

        return response()->json($question, 200);
    }

    // 🔹 Actualizar una pregunta
    public function update(Request $request, $id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Pregunta no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'question_text' => 'sometimes|string',
            'question_type' => 'sometimes|string|in:multiple_choice,true_false',
            'category_id'   => 'sometimes|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question->update($request->only(['question_text', 'question_type', 'category_id']));

        return response()->json($question, 200);
    }

    // 🔹 Eliminar una pregunta
    public function destroy($id)
    {
        $question = Question::find($id);

        if (!$question) {
            return response()->json(['message' => 'Pregunta no encontrada'], 404);
        }

        $question->delete();

        return response()->json(['message' => 'Pregunta eliminada'], 200);
    }
}
