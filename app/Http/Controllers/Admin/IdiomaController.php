<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Idioma;
use Illuminate\Http\Request;

class IdiomaController extends Controller
{
    public function index(Request $request)
    {
        $query = Idioma::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                ->orWhere('codigo', 'like', "%{$request->buscar}%");
        }

        $idiomas = $query->latest()->paginate(15);

        return view('admin.idiomas.index', compact('idiomas'));
    }

    public function create()
    {
        return view('admin.idiomas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:idiomas,nombre',
            'codigo' => 'nullable|string|max:10|unique:idiomas,codigo',
        ]);

        Idioma::create($validated);

        return redirect()
            ->route('admin.idiomas.index')
            ->with('success', 'Idioma creado correctamente.');
    }

    public function show(Idioma $idioma)
    {
        return view('admin.idiomas.show', compact('idioma'));
    }

    public function edit(Idioma $idioma)
    {
        return view('admin.idiomas.edit', compact('idioma'));
    }

    public function update(Request $request, Idioma $idioma)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:idiomas,nombre,' . $idioma->id,
            'codigo' => 'nullable|string|max:10|unique:idiomas,codigo,' . $idioma->id,
        ]);

        $idioma->update($validated);

        return redirect()
            ->route('admin.idiomas.index')
            ->with('success', 'Idioma actualizado correctamente.');
    }

    public function destroy(Idioma $idioma)
    {
        if ($idioma->clientes()->exists()) {
            return back()->with('error', 'No se puede eliminar este idioma porque está asignado a uno o más clientes.');
        }

        $idioma->delete();

        return redirect()
            ->route('admin.idiomas.index')
            ->with('success', 'Idioma eliminado correctamente.');
    }

    public function buscar(Request $request)
    {
        $idiomas = Idioma::where('nombre', 'like', "%{$request->q}%")
            ->orWhere('codigo', 'like', "%{$request->q}%")
            ->select('id', 'nombre', 'codigo')
            ->orderBy('nombre')
            ->limit(10)
            ->get();

        return response()->json($idiomas);
    }
}
