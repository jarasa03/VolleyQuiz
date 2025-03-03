<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagsController extends Controller
{
    // Obtener todas las etiquetas
    public function index()
    {
        return response()->json(Tag::all(), 200);
    }

    // Crear una nueva etiqueta
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags,name|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tag = Tag::create(['name' => $request->name]);

        return response()->json($tag, 201);
    }

    // Obtener una etiqueta por ID
    public function show($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Etiqueta no encontrada'], 404);
        }

        return response()->json($tag, 200);
    }

    // Actualizar una etiqueta
    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Etiqueta no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:tags,name|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tag->update(['name' => $request->name]);

        return response()->json($tag, 200);
    }

    // Eliminar una etiqueta
    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json(['message' => 'Etiqueta no encontrada'], 404);
        }

        $tag->delete();

        return response()->json(['message' => 'Etiqueta eliminada'], 200);
    }
}
