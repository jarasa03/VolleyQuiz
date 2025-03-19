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
        $questions = Question::with(['category', 'answers', 'tags'])->paginate(10); // Agregamos paginación

        return view('admin.questions.index', compact('questions'));
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

    // Asignar etiquetas a una pregunta
    public function attachTags(Request $request, $id)
    {
        $question = Question::find($id);
        if (!$question) {
            return response()->json(['message' => 'Pregunta no encontrada'], 404);
        }

        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'exists:tags,id', // Cada ID de tag debe existir en la tabla tags
        ]);

        $question->tags()->syncWithoutDetaching($request->tags); // Evita duplicados

        return response()->json(['message' => 'Etiquetas asignadas con éxito'], 200);
    }

    // Eliminar una etiqueta de una pregunta
    public function detachTag($question_id, $tag_id)
    {
        $question = Question::find($question_id);
        if (!$question) {
            return response()->json(['message' => 'Pregunta no encontrada'], 404);
        }

        $question->tags()->detach($tag_id);

        return response()->json(['message' => 'Etiqueta eliminada con éxito'], 200);
    }
}
