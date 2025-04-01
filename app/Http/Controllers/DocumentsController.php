<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends Controller
{
    // Obtener todos los documentos
    public function index()
    {
        return response()->json(Document::all(), 200);
    }

    // Mostrar la vista del dashboard de documentación
    public function dashboard()
    {
        return view('documentation.dashboard');
    }

    public function verSeccion($seccion)
    {
        $vista = match ($seccion) {
            'general' => 'documentation.general',
            'fivb' => 'documentation.fivb',
            'rfevb' => 'documentation.rfevb',
            'fmvb' => 'documentation.territoriales.fmvb',
            default => null,
        };

        if (!$vista || !view()->exists($vista)) {
            abort(404);
        }

        return view($vista, ['seccion' => strtoupper($seccion)]);
    }

    // Subir un nuevo documento
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'section_id' => 'required|exists:document_sections,id',
            'file' => 'required|file|mimes:pdf|max:5120', // Solo permite PDFs de máximo 5MB
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Guardar el archivo en storage/app/documents
        $path = $request->file('file')->store('documents');

        // Crear documento en la base de datos
        $document = Document::create([
            'title' => $request->title,
            'section_id' => $request->section_id,
            'file_path' => $path,
        ]);

        return response()->json($document, 201);
    }

    // Obtener un documento por ID
    public function show($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        return response()->json($document, 200);
    }

    // Descargar un documento
    public function download($id)
    {
        $document = Document::find($id);

        if (!$document || !Storage::exists($document->file_path)) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        return Storage::download($document->file_path);
    }

    // Actualizar un documento
    public function update(Request $request, $id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'section_id' => 'sometimes|exists:document_sections,id',
            'file' => 'sometimes|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Si se sube un nuevo archivo, eliminamos el anterior
        if ($request->hasFile('file')) {
            Storage::delete($document->file_path);
            $document->file_path = $request->file('file')->store('documents');
        }

        $document->update($request->only(['title', 'section_id']));

        return response()->json($document, 200);
    }

    // Eliminar un documento
    public function destroy($id)
    {
        $document = Document::find($id);

        if (!$document) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        Storage::delete($document->file_path);
        $document->delete();

        return response()->json(['message' => 'Documento eliminado'], 200);
    }
}
