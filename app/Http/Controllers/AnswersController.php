<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Controlador para la gestión de respuestas en el sistema.
 * 
 * Proporciona funcionalidades para crear, actualizar, eliminar y obtener respuestas.
 */
class AnswersController extends Controller
{
    /**
     * Obtiene todas las respuestas almacenadas en la base de datos.
     * 
     * Se devuelven las respuestas en formato JSON con un límite de 10 respuestas por página.
     * 
     * @return \Illuminate\Http\JsonResponse Lista de respuestas en formato JSON.
     */
    public function index()
    {
        return response()->json(Answer::paginate(10), 200);
    }

    /**
     * Almacena una nueva respuesta en la base de datos.
     * 
     * Se requiere la asociación con una pregunta existente y la validación de los datos proporcionados.
     * 
     * @param \Illuminate\Http\Request $request Datos de la respuesta a crear.
     * @return \Illuminate\Http\JsonResponse La respuesta creada o errores de validación.
     */
    public function store(Request $request)
    {
        // Se validan los datos de entrada
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'required|string|max:255',
            'is_correct' => 'required|boolean',
        ]);

        // Se devuelve un error si la validación falla
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Se crea y almacena la nueva respuesta
        $answer = Answer::create($request->all());
        return response()->json($answer, 201);
    }

    /**
     * Obtiene una respuesta específica por su ID.
     * 
     * @param int $id ID de la respuesta a recuperar.
     * @return \Illuminate\Http\JsonResponse La respuesta encontrada o un mensaje de error si no existe.
     */
    public function show($id)
    {
        // Se busca la respuesta en la base de datos
        $answer = Answer::find($id);

        // Se devuelve un error si no se encuentra la respuesta
        if (!$answer) {
            return response()->json(['message' => 'Respuesta no encontrada'], 404);
        }

        return response()->json($answer, 200);
    }

    /**
     * Actualiza una respuesta existente en la base de datos.
     * 
     * Se permite actualizar solo los campos proporcionados en la solicitud.
     * 
     * @param \Illuminate\Http\Request $request Datos a actualizar en la respuesta.
     * @param int $id ID de la respuesta a actualizar.
     * @return \Illuminate\Http\JsonResponse La respuesta actualizada o un mensaje de error si no se encuentra.
     */
    public function update(Request $request, $id)
    {
        // Se busca la respuesta en la base de datos
        $answer = Answer::find($id);

        // Se devuelve un error si la respuesta no existe
        if (!$answer) {
            return response()->json(['message' => 'Respuesta no encontrada'], 404);
        }

        // Se validan los datos de entrada
        $validator = Validator::make($request->all(), [
            'answer_text' => 'sometimes|string|max:255',
            'is_correct' => 'sometimes|boolean',
        ]);

        // Se devuelve un error si la validación falla
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Se actualizan los datos de la respuesta
        $answer->update($request->all());
        return response()->json($answer, 200);
    }

    /**
     * Elimina una respuesta de la base de datos.
     * 
     * @param int $id ID de la respuesta a eliminar.
     * @return \Illuminate\Http\JsonResponse Mensaje de confirmación o error si no se encuentra la respuesta.
     */
    public function destroy($id)
    {
        // Se busca la respuesta en la base de datos
        $answer = Answer::find($id);

        // Se devuelve un error si la respuesta no existe
        if (!$answer) {
            return response()->json(['message' => 'Respuesta no encontrada'], 404);
        }

        // Se elimina la respuesta de la base de datos
        $answer->delete();
        return response()->json(['message' => 'Respuesta eliminada'], 200);
    }

    /**
     * Obtiene todas las respuestas de una pregunta específica.
     * 
     * @param int $questionId ID de la pregunta asociada.
     * @return \Illuminate\Http\JsonResponse Lista de respuestas de la pregunta.
     */
    public function getByQuestion($questionId)
    {
        $answers = Answer::where('question_id', $questionId)->get();
        return response()->json($answers, 200);
    }
}
