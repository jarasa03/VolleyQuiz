<?php

namespace App\Http\Controllers;

use App\Models\Test;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    // Obtener todos los tests
    public function index()
    {
        return response()->json(Test::all(), 200);
    }

    // Crear un nuevo test
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'difficulty' => 'required|string|in:bajo,medio,alto', // Solo permite valores específicos
            'category_id' => 'required|exists:categories,id', // Debe existir en la tabla categories
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $test = Test::create([
            'title' => $request->title,
            'difficulty' => $request->difficulty,
            'category_id' => $request->category_id,
        ]);

        return response()->json($test, 201);
    }

    // Obtener un test por ID
    public function show($id)
    {
        $test = Test::find($id);

        if (!$test) {
            return response()->json(['message' => 'Test no encontrado'], 404);
        }

        return response()->json($test, 200);
    }

    // Actualizar un test
    public function update(Request $request, $id)
    {
        $test = Test::find($id);

        if (!$test) {
            return response()->json(['message' => 'Test no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'difficulty' => 'sometimes|string|in:bajo,medio,alto',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $test->update($request->only(['title', 'difficulty', 'category_id']));

        return response()->json($test, 200);
    }

    // Eliminar un test
    public function destroy($id)
    {
        $test = Test::find($id);

        if (!$test) {
            return response()->json(['message' => 'Test no encontrado'], 404);
        }

        $test->delete();

        return response()->json(['message' => 'Test eliminado'], 200);
    }
}
