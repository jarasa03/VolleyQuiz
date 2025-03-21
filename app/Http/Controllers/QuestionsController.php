<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use App\Traits\AuthHelpers;
use App\Traits\AdminMiddleware;

/**
 * Controlador para la gestión de preguntas.
 */
class QuestionsController extends Controller
{
    use AuthHelpers, AdminMiddleware;

    public function __construct()
    {
        $this->applyAdminMiddleware(); // Llamamos al método del Trait en lugar de definir el constructor manualmente
    }

    // Mostrar la lista de preguntas con búsqueda
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = Question::with(['category', 'answers', 'tags']);

        if ($search) {
            $query->where('question_text', 'LIKE', "%{$search}%");
        }

        $questions = $query->orderBy('id', 'asc')->paginate(10);
        return view('admin.questions.index', compact('questions', 'search'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.questions.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:multiple_choice,true_false',
            'category_id'   => 'required|exists:categories,id',
        ]);

        $question = Question::create($validatedData);

        // ✅ Guardar respuestas según el tipo de pregunta
        if ($validatedData['question_type'] === 'multiple_choice') {
            $answers = $request->input('answers', []);

            foreach ($answers as $answerData) {
                if (!empty($answerData['text'])) {
                    $question->answers()->create([
                        'answer_text' => $answerData['text'],
                        'is_correct' => isset($answerData['correct']) && $answerData['correct'] == '1'
                    ]);
                }
            }
        } elseif ($validatedData['question_type'] === 'true_false') {
            $correctAnswer = $request->input('correct_answer'); // 'true' o 'false'

            $question->answers()->create([
                'answer_text' => 'true',
                'is_correct' => $correctAnswer === 'true'
            ]);

            $question->answers()->create([
                'answer_text' => 'false',
                'is_correct' => $correctAnswer === 'false'
            ]);
        }

        // ✅ Asignar tags (deserializando el string JSON del input oculto)
        $tagIds = json_decode($request->input('tags', '[]'), true);

        if (is_array($tagIds) && !empty($tagIds)) {
            $validTags = Tag::whereIn('id', $tagIds)->pluck('id')->toArray();
            $question->tags()->sync($validTags);
        }

        return redirect()->route('admin.questions.index')->with('success', '✅ Pregunta creada con éxito.');
    }






    // Mostrar formulario de edición
    public function edit($id)
    {
        $question = Question::findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.questions.edit', compact('question', 'categories', 'tags'));
    }

    // Actualizar una pregunta
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        // Asegurar que los tags llegan como array de enteros
        $tagsArray = array_map('intval', explode(',', $request->input('tags', '')));
        $request->merge(['tags' => $tagsArray]);

        $validatedData = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:multiple_choice,true_false',
            'category_id'   => 'required|exists:categories,id',
            'tags'          => 'nullable|array',
            'tags.*'        => 'exists:tags,id',
        ]);

        $question->update($validatedData);

        $tagIds = $request->input('tags', []);
        $validTags = Tag::whereIn('id', $tagIds)->pluck('id')->toArray();
        $question->tags()->sync($validTags);


        // 🧹 Limpiar respuestas anteriores
        $question->answers()->delete();

        // 🔄 Reinsertar según tipo de pregunta
        if ($validatedData['question_type'] === 'multiple_choice') {
            $answers = $request->input('answers', []);
            $correctFlags = $request->input('correct_answers', []);

            foreach ($answers as $index => $text) {
                if (!empty($text)) {
                    $question->answers()->create([
                        'answer_text' => $text,
                        'is_correct' => isset($correctFlags[$index]) && $correctFlags[$index] == '1'
                    ]);
                }
            }
        } elseif ($validatedData['question_type'] === 'true_false') {
            $correctAnswer = $request->input('correct_answer');

            $question->answers()->create([
                'answer_text' => 'true',
                'is_correct' => $correctAnswer === 'true'
            ]);

            $question->answers()->create([
                'answer_text' => 'false',
                'is_correct' => $correctAnswer === 'false'
            ]);
        }

        return redirect()->route('admin.questions.index')->with('success', '✅ Pregunta actualizada correctamente.');
    }


    // Eliminar una pregunta
    public function destroy($id)
    {
        $question = Question::findOrFail($id);

        // Eliminar respuestas asociadas antes de eliminar la pregunta
        $question->answers()->delete();

        // También eliminamos los tags relacionados (opcional, Laravel lo hace solo con sync)
        $question->tags()->detach();

        $question->delete();

        return redirect()->route('admin.questions.index')->with('success', '✅ Pregunta eliminada correctamente.');
    }
}
