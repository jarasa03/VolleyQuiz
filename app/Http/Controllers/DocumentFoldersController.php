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
        $secciones = DocumentSection::with(['folders' => function ($query) use ($request) {
            $query->whereNull('parent_id')->with('children');

            if ($request->has('search') && $request->search !== '') {
                $searchTerm = $request->search;

                $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhereHas('children', function ($q) use ($searchTerm) {
                        $q->where('name', 'like', "%{$searchTerm}%");
                    });
            }
        }])->get();

        return view('admin.folders.index', compact('secciones'));
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
        // Validación básica sin la regla 'unique'
        $request->validate([
            'name' => 'required|string|max:255',  // Eliminamos 'unique' para no hacer la validación automática
            'section_id' => 'required|exists:document_sections,id',
            'parent_id' => 'nullable|exists:document_folders,id'
        ]);

        // Comprobar si ya existe una carpeta con ese nombre en la misma sección
        $existeCarpeta = DocumentFolder::where('name', $request->name)
            ->where('section_id', $request->section_id)
            ->first();

        if ($existeCarpeta) {
            // Si ya existe la carpeta, redirigir con un mensaje de error
            return redirect()->back()
                ->with('error', '⚠️ Ya existe una carpeta con ese nombre en esta sección.')
                ->withInput();
        }

        // Si no existe, crear la carpeta
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

        $ruta = $this->obtenerRutaCarpeta($carpeta); // e.g. fmvb/reglamento
        $rutaCompleta = "documents/{$ruta}";

        // 🧽 Asegurarse de que no tenga documentos ni subcarpetas
        if (
            $carpeta->documents()->count() === 0 &&
            $carpeta->children()->count() === 0
        ) {
            // 🧹 Eliminar carpeta física si está vacía
            if (
                Storage::disk('public')->exists($rutaCompleta) &&
                count(Storage::disk('public')->files($rutaCompleta)) === 0 &&
                count(Storage::disk('public')->directories($rutaCompleta)) === 0
            ) {
                Storage::disk('public')->deleteDirectory($rutaCompleta);
            }

            // 🗑 Eliminar registro
            $carpeta->delete();

            return redirect()->route('admin.folders.index')
                ->with('message', '🗑 Carpeta eliminada correctamente.');
        }

        // ❌ Si no está vacía, avisar al usuario
        return redirect()->route('admin.folders.index')
            ->with('error', '⚠️ No se puede eliminar una carpeta que contiene subcarpetas o documentos.');
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
