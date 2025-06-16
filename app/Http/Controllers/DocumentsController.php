<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\DocumentSection;
use App\Models\DocumentFolder;

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
            'file' => 'required|file|mimes:pdf,docx,doc,xls,xlsx|max:5120',
            'folder_id' => 'nullable|exists:document_folders,id',
            'year' => 'nullable|string|max:9',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $section = DocumentSection::findOrFail($request->section_id);

        // 🧭 Obtener ruta completa de carpeta (o solo sección si no hay folder_id)
        $folderPath = $request->folder_id
            ? DocumentFolder::findOrFail($request->folder_id)->buildPath()
            : strtolower($section->name);

        // 🧼 Normalizar nombre del archivo
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

        $filename = $slugTitle . '.' . $request->file('file')->getClientOriginalExtension();
        $path = "documents/$folderPath/$filename";

        if (Storage::disk('public')->exists($path)) {
            return redirect()->back()
                ->with('error', '⚠️ Ya existe un documento con ese nombre en esta sección.')
                ->withInput();
        }

        // ✅ Subir el archivo
        Storage::disk('public')->putFileAs("documents/$folderPath", $request->file('file'), $filename);

        // 📝 Crear entrada en BD
        Document::create([
            'title' => $request->title,
            'section_id' => $request->section_id,
            'folder_id' => $request->folder_id,
            'file_path' => $path,
            'year' => $request->year ?? null,  // Guardar el año si está presente
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
        $section = DocumentSection::where('name', $seccion)->firstOrFail();

        $carpetas = DocumentFolder::where('section_id', $section->id)
            ->whereNull('parent_id')
            ->orderBy('name') // 👈 orden alfabético por nombre
            ->get();

        $documentos = Document::where('section_id', $section->id)
            ->whereNull('folder_id')
            ->orderBy('title') // 👈 orden alfabético por título
            ->get();

        return view('documentation.folder', [
            'seccion' => strtoupper($seccion),
            'section' => $section,
            'carpeta' => null,
            'subcarpetas' => $carpetas,
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

        if (!$document || !Storage::disk('public')->exists($document->file_path)) {
            return response()->json(['message' => 'Documento no encontrado'], 404);
        }

        $filePath = storage_path('app/public/' . $document->file_path);
        return response()->download($filePath, basename($filePath));
    }

    public function update(Request $request, $id)
    {
        $document = Document::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'section_id' => 'required|exists:document_sections,id',
            'file' => 'nullable|file|mimes:pdf,docx,doc,xls,xlsx|max:5120',
            'folder_id' => 'nullable|exists:document_folders,id',
            'year' => 'nullable|string|max:9',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $section = DocumentSection::findOrFail($request->section_id);

        // 🧭 Obtener ruta correcta
        $folderPath = $request->folder_id
            ? DocumentFolder::findOrFail($request->folder_id)->buildPath()
            : strtolower($section->name);

        // 🧼 Normalizar nombre
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

        // Solo generar el nombre del archivo si se ha subido uno nuevo
        if ($request->hasFile('file')) {
            $filename = $slugTitle . '.' . $request->file('file')->getClientOriginalExtension();
            $newPath = "documents/$folderPath/$filename";

            if (Storage::disk('public')->exists($newPath) && $newPath !== $document->file_path) {
                return redirect()->back()
                    ->with('error', '⚠️ Ya existe un documento con ese nombre en esta carpeta.')
                    ->withInput();
            }

            // Borrar el archivo anterior si se va a reemplazar
            Storage::disk('public')->delete($document->file_path);
            // Subir el nuevo archivo
            Storage::disk('public')->putFileAs("documents/$folderPath", $request->file('file'), $filename);

            // Actualizar la ruta del archivo en la base de datos
            $document->file_path = $newPath;
        }


        // Actualizar los demás campos
        $document->update([
            'title' => $request->title,
            'section_id' => $request->section_id,
            'folder_id' => $request->folder_id,
            'year' => $request->year,  // Actualiza el año
        ]);

        return redirect()->route('admin.documents.index')
            ->with('message', '✅ Documento actualizado correctamente.');
    }




    public function destroy($id)
    {
        $document = Document::findOrFail($id);

        $filePath = $document->file_path;

        // 🗑 Borrar archivo del disco (public)
        if (Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        // ✅ Eliminar registro de base de datos
        $document->delete();

        // 📂 OPCIONAL: borrar carpeta si queda vacía (solo si no tiene más archivos)
        $folderDir = dirname($filePath);
        if (
            Storage::disk('public')->exists($folderDir) &&
            count(Storage::disk('public')->files($folderDir)) === 0 &&
            count(Storage::disk('public')->directories($folderDir)) === 0
        ) {
            Storage::disk('public')->deleteDirectory($folderDir);
        }

        return redirect()->route('admin.documents.index')->with('message', '🗑 Documento eliminado correctamente.');
    }


    public function verCarpeta($id)
    {
        $carpeta = DocumentFolder::with('section')->findOrFail($id);

        $subcarpetas = $carpeta->children()->orderBy('name')->get(); // 👈 Añade esto
        $documentos = $carpeta->documents()->orderBy('title')->get(); // 👈 Y esto también

        // 🧭 Breadcrumb
        $breadcrumb = [];
        $actual = $carpeta;
        while ($actual) {
            $breadcrumb[] = $actual;
            $actual = $actual->parent;
        }
        $breadcrumb = array_reverse($breadcrumb);

        return view('documentation.folder', [
            'section' => $carpeta->section,
            'seccion' => strtoupper($carpeta->section->name),
            'carpeta' => $carpeta,
            'subcarpetas' => $subcarpetas,
            'documentos' => $documentos,
            'breadcrumb' => $breadcrumb,
        ]);
    }
}
