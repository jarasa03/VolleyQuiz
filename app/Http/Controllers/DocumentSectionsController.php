<?php

namespace App\Http\Controllers;

use App\Models\DocumentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentSectionsController extends Controller
{
    // 🔹 Obtener todas las secciones de documentos
    public function index()
    {
        return response()->json(DocumentSection::all(), 200);
    }

    // 🔹 Crear una nueva sección de documentos
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:document_sections',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $section = DocumentSection::create(['name' => $request->name]);

        return response()->json($section, 201);
    }

    // 🔹 Obtener una sección de documentos por ID
    public function show($id)
    {
        $section = DocumentSection::find($id);

        if (!$section) {
            return response()->json(['message' => 'Sección no encontrada'], 404);
        }

        return response()->json($section, 200);
    }

    // 🔹 Actualizar una sección de documentos
    public function update(Request $request, $id)
    {
        $section = DocumentSection::find($id);

        if (!$section) {
            return response()->json(['message' => 'Sección no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|unique:document_sections,name,' . $id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $section->update($request->only('name'));

        return response()->json($section, 200);
    }

    // 🔹 Eliminar una sección de documentos
    public function destroy($id)
    {
        $section = DocumentSection::find($id);

        if (!$section) {
            return response()->json(['message' => 'Sección no encontrada'], 404);
        }

        $section->delete();

        return response()->json(['message' => 'Sección eliminada'], 200);
    }
}
