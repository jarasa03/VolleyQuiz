<?php

namespace App\Http\Controllers;

use App\Models\DocumentFolder;
use App\Models\DocumentSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentFoldersController extends Controller
{
    // 📁 Mostrar todas las carpetas principales con sus subcarpetas
    public function index(Request $request)
    {
        $query = DocumentFolder::with('children')->whereNull('parent_id');

        if ($request->has('search') && $request->search !== '') {
            $searchTerm = $request->search;

            $query->where('name', 'like', "%{$searchTerm}%")
                ->orWhereHas('children', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%");
                });
        }

        $carpetas = $query->get();

        return view('admin.folders.index', compact('carpetas'));
    }

    // ➕ Mostrar formulario para crear carpeta
    public function create()
    {
        $carpetasPadre = DocumentFolder::with('childrenRecursive')
            ->whereNull('parent_id')
            ->get();

        $secciones = DocumentSection::all();

        return view('admin.folders.create', compact('carpetasPadre', 'secciones'));
    }

    // 💾 Guardar nueva carpeta
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:document_folders,name',
            'section_id' => 'required|exists:document_sections,id',
            'parent_id' => 'nullable|exists:document_folders,id'
        ]);

        $carpeta = DocumentFolder::create($request->only('name', 'parent_id', 'section_id'));

        // 🛠 Crear carpeta en disco
        $ruta = $this->obtenerRutaCarpeta($carpeta);
        Storage::makeDirectory("documents/{$ruta}");

        return redirect()->route('admin.folders.index')
            ->with('message', '📁 Carpeta creada correctamente.');
    }

    // ✏️ Mostrar formulario de edición de carpeta
    public function edit($id)
    {
        $carpeta = DocumentFolder::findOrFail($id);
        $carpetasPadre = DocumentFolder::with('children')
            ->where('id', '!=', $id)
            ->get();
        $secciones = DocumentSection::all();

        return view('admin.folders.edit', compact('carpeta', 'carpetasPadre', 'secciones'));
    }

    // 🔁 Actualizar una carpeta existente
    public function update(Request $request, $id)
    {
        $carpeta = DocumentFolder::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:document_folders,name,' . $carpeta->id,
            'section_id' => 'required|exists:document_sections,id',
            'parent_id' => 'nullable|exists:document_folders,id|not_in:' . $carpeta->id,
        ]);

        $carpeta->update($request->only('name', 'parent_id', 'section_id'));

        return redirect()->route('admin.folders.index')
            ->with('message', '✅ Carpeta actualizada correctamente.');
    }

    // 🗑 Eliminar una carpeta
    public function destroy($id)
    {
        $carpeta = DocumentFolder::findOrFail($id);
        $ruta = $this->obtenerRutaCarpeta($carpeta);
        $rutaCompleta = "documents/{$ruta}";

        if (Storage::exists($rutaCompleta) && count(Storage::files($rutaCompleta)) === 0 && count(Storage::directories($rutaCompleta)) === 0) {
            Storage::deleteDirectory($rutaCompleta);
        }

        $carpeta->delete();

        return redirect()->route('admin.folders.index')
            ->with('message', '🗑 Carpeta eliminada correctamente.');
    }

    // 🔧 Obtener ruta completa recursiva desde la raíz
    private function obtenerRutaCarpeta(DocumentFolder $carpeta)
    {
        $segmentos = [];
        $actual = $carpeta;

        while ($actual) {
            array_unshift($segmentos, Str::slug($actual->name));
            $actual = $actual->parent;
        }

        $seccion = strtolower($carpeta->section->name);
        return $seccion . '/' . implode('/', $segmentos);
    }

    // 🧠 Obtener carpetas por sección (recursivo)
    public function carpetasPorSeccion($sectionId)
    {
        $carpetas = DocumentFolder::with('childrenRecursive')
            ->where('section_id', $sectionId)
            ->whereNull('parent_id')
            ->get();

        return response()->json($carpetas);
    }
}
