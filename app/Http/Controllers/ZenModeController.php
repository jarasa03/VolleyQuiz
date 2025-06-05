<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Question;

class ZenModeController extends Controller
{
    public function index()
    {
        return view('zen.index');
    }

    public function start(Request $request)
    {
        $questions = Question::inRandomOrder()->limit(10)->get();

        session([
            'zen.questions' => $questions->pluck('id')->toArray(),
            'zen.answers' => [],
            'zen.correct' => [],
        ]);

        return redirect()->route('zen.question', ['index' => 0]);
    }

    public function question($index)
    {
        $questionIds = session('zen.questions', []);

        if (!isset($questionIds[$index])) {
            return redirect()->route('zen.result');
        }

        $question = Question::with(['answers', 'explanation'])->findOrFail($questionIds[$index]);

        return view('zen.question', [
            'question' => $question,
            'index' => $index,
        ]);
    }

    public function answer(Request $request, $index)
    {
        $questionIds = session('zen.questions', []);
        $questionId = $questionIds[$index] ?? null;

        if (!$questionId) {
            return redirect()->route('zen.result');
        }

        $question = Question::with(['answers', 'explanation'])->findOrFail($questionId);
        $correctAnswers = $question->answers->where('is_correct', true)->pluck('id')->sort()->values();
        $isMultiple = $question->question_type === 'multiple_choice';

        if ($isMultiple) {
            $request->validate([
                'respuesta_id' => 'required|array',
                'respuesta_id.*' => 'integer',
            ]);

            $selected = collect($request->respuesta_id)->map(fn($id) => (int) $id)->sort()->values();
            $esCorrecta = $selected->toArray() === $correctAnswers->toArray();
            $respuestaSeleccionada = $selected->toArray();
        } else {
            $request->validate([
                'respuesta_id' => 'required|integer',
            ]);

            $selected = (int) $request->respuesta_id;
            $esCorrecta = $selected === $correctAnswers->first();
            $respuestaSeleccionada = $selected;
        }

        // Guardar en sesión
        $answers = session('zen.answers', []);
        $correct = session('zen.correct', []);

        $answers[$index] = $respuestaSeleccionada;
        $correct[$index] = $esCorrecta;

        session([
            'zen.answers' => $answers,
            'zen.correct' => $correct,
        ]);

        return view('zen.explanation', [
            'question' => $question,
            'respuestaSeleccionada' => $respuestaSeleccionada,
            'respuestaCorrecta' => $correctAnswers,
            'esCorrecta' => $esCorrecta,
            'index' => $index,
            'ultima' => $index == 9,
        ]);
    }


    public function result()
    {
        $correct = session('zen.correct', []);
        $aciertos = collect($correct)->filter()->count();

        session()->forget(['zen.questions', 'zen.answers', 'zen.correct']);

        return view('zen.result', [
            'aciertos' => $aciertos,
            'total' => 10,
        ]);
    }
}
