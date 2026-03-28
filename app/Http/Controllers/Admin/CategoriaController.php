<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                ->orWhere('descripcion', 'like', "%{$request->buscar}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === '1');
        }

        $categorias = $query->latest()->paginate(15);

        return view('admin.categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'icono'       => 'nullable|string|max:100',
            'estado'      => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        Categoria::create($validated);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function show(Categoria $categoria)
    {
        $categoria->load('tours');
        return view('admin.categorias.show', compact('categoria'));
    }

    public function edit(Categoria $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'icono'       => 'nullable|string|max:100',
            'estado'      => 'nullable|boolean',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        $categoria->update($validated);

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->tours()->exists()) {
            return back()->with('error', 'No se puede eliminar esta categoría porque tiene tours asociados.');
        }

        $categoria->delete();

        return redirect()
            ->route('admin.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
