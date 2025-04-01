<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentSection;

class DocumentsController extends Controller
{
    // Obtener todos los documentos
    public function index()
    {
        return response()->json(Document::all(), 200);
    }

    public function create()
    {
        $secciones = DocumentSection::all();

        return view('admin.documents.create', compact('secciones'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'section_id' => 'required|exists:document_sections,id',
            'file' => 'required|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $section = DocumentSection::findOrFail($request->section_id);
        $folder = strtolower($section->name);

        // 🧼 Normalizar tildes manualmente
        $slugTitle = strtolower($request->title);
        $slugTitle = strtr($slugTitle, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'ñ' => 'n',
            'ä' => 'a',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'o',
            'ü' => 'u'
        ]);

        $slugTitle = preg_replace('/[^a-z0-9]+/i', '-', $slugTitle);
        $slugTitle = trim($slugTitle, '-');


        // 📎 Montar el nombre del archivo
        $filename = $slugTitle . '.' . $request->file('file')->getClientOriginalExtension();
        $path = "documents/$folder/$filename";

        // ❌ Si ya existe, error
        if (Storage::exists($path)) {
            return redirect()->back()
                ->with('error', '⚠️ Ya existe un documento con ese nombre en esta sección.')
                ->withInput();
        }

        // ✅ Guardar archivo y crear entrada en base de datos
        $request->file('file')->storeAs("documents/$folder", $filename);

        Document::create([
            'title' => $request->title,
            'section_id' => $request->section_id,
            'file_path' => $path,
        ]);

        return redirect()->route('admin.documents.index')
            ->with('message', '✅ Documento subido correctamente.');
    }


    public function adminIndex(Request $request)
    {
        $documentos = Document::with('section')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%$search%")
                    ->orWhereHas('section', fn($q) => $q->where('name', 'like', "%$search%"));
            })
            ->paginate(10); // 👈 esto es clave

        return view('admin.documents.index', compact('documentos'));
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

        // Obtener documentos según el nombre de la sección
        $documentos = Document::whereHas(
            'section',
            fn($q) =>
            $q->where('name', $seccion)
        )->get();

        return view($vista, [
            'seccion' => strtoupper($seccion),
            'documentos' => $documentos
        ]);
    }

    public function edit($id)
    {
        $documento = Document::findOrFail($id);
        $secciones = DocumentSection::all();

        return view('admin.documents.edit', compact('documento', 'secciones'));
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

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'section_id' => 'required|exists:document_sections,id',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 🗂️ Obtener sección actualizada
        $section = DocumentSection::findOrFail($request->section_id);
        $folder = strtolower($section->name);

        // 🧼 Normalizar el título (acentos y símbolos raros)
        $slugTitle = strtolower($request->title);
        $slugTitle = strtr($slugTitle, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U',
            'ñ' => 'n',
            'Ñ' => 'N',
            'ä' => 'a',
            'ë' => 'e',
            'ï' => 'i',
            'ö' => 'o',
            'ü' => 'u'
        ]);
        $slugTitle = preg_replace('/[^a-z0-9]+/i', '-', $slugTitle);
        $slugTitle = trim($slugTitle, '-');

        $filename = $slugTitle . '.pdf';
        $newPath = "documents/$folder/$filename";

        if ($request->hasFile('file')) {
            // 🚫 Prevenir sobreescritura si ya existe otro con ese nombre en esta sección
            if (Storage::exists($newPath) && $newPath !== $document->file_path) {
                return redirect()->back()
                    ->with('error', '⚠️ Ya existe un documento con ese nombre en esta sección.')
                    ->withInput();
            }

            // 🧹 Borrar el anterior y subir el nuevo
            Storage::delete($document->file_path);
            $request->file('file')->storeAs("documents/$folder", $filename);
            $document->file_path = $newPath;
        } elseif (
            $document->section_id != $request->section_id ||
            basename($document->file_path) !== $filename
        ) {
            // ✋ Mover el archivo si cambió la sección o el título
            if (Storage::exists($newPath)) {
                return redirect()->back()
                    ->with('error', '⚠️ Ya existe un documento con ese nombre en la nueva sección.')
                    ->withInput();
            }

            Storage::move($document->file_path, $newPath);
            $document->file_path = $newPath;
        }

        // 📝 Actualizar datos del documento
        $document->update([
            'title' => $request->title,
            'section_id' => $request->section_id,
        ]);

        return redirect()->route('admin.documents.index')
            ->with('message', '✅ Documento actualizado correctamente.');
    }


    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        Storage::delete($document->file_path);
        $document->delete();

        return redirect()->route('admin.documents.index')->with('message', '🗑 Documento eliminado correctamente.');
    }
}
