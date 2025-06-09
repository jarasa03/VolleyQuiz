<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use App\Traits\AuthHelpers;
use App\Traits\AdminMiddleware;

/**
 * Controlador para la gestión de Tags.
 */
class TagsController extends Controller
{
    use AuthHelpers, AdminMiddleware;

    public function __construct()
    {
        $this->applyAdminMiddleware(); // 🔹 Llamamos al método del Trait en lugar de definir el constructor manualmente
    }

    /**
     * Muestra la lista de tags con paginación.
     */
    public function index(Request $request)
    {
        // Obtener el término de búsqueda desde la URL
        $search = $request->input('search');

        // Iniciar la consulta
        $query = Tag::query();

        // Filtrar si hay una búsqueda activa
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Obtener los tags paginados
        $tags = $query->orderBy('id', 'asc')->paginate(10);

        return view('admin.tags.index', compact('tags'));
    }


    /**
     * Muestra el formulario para crear un nuevo tag.
     */
    public function create()
    {
        return view('admin.tags.create');
    }

    /**
     * Almacena un nuevo tag en la base de datos.
     */

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/', // HEX válido
        ]);

        $existingTag = Tag::where('name', $request->name)->first();
        if ($existingTag) {
            return redirect()->route('admin.tags.create')
                ->with('error', '❌ Ya existe un tag con ese nombre. Por favor elige otro nombre.');
        }

        Tag::create($validatedData);

        return redirect()->route('admin.tags.index')->with('message', '✅ Tag creada con éxito.');
    }


    /**
     * Muestra el formulario para editar un tag existente.
     */
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);
        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Actualiza un tag en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $id,
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/', // 🔹 HEX válido
        ]);

        $tag->update($validatedData);

        return redirect()->route('admin.tags.index')->with('message', '✅ Tag actualizada correctamente.');
    }

    /**
     * Elimina un tag de la base de datos.
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);

        // 🔹 Elimina las relaciones con preguntas (tabla pivote question_tag)
        $tag->questions()->detach();

        // 🔹 Ahora puedes eliminar el tag sin error
        $tag->delete();

        return redirect()->route('admin.tags.index')->with('message', '✅ Tag eliminado correctamente.');
    }
}
