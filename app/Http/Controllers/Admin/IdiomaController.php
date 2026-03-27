<?php

namespace App\Http\Controllers;

use App\Models\Idioma;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IdiomaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Idioma::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                ->orWhere('codigo', 'like', "%{$request->buscar}%");
        }

        $idiomas = $query->latest()->paginate(15);

        return view('idiomas.index', compact('idiomas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('idiomas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(Idioma $idioma)
    {
        return view('idiomas.show', compact('idioma'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idioma $idioma)
    {
        return view('idiomas.edit', compact('idioma'));
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idioma $idioma)
    {
        // Verificar si el idioma está siendo usado por clientes
        if ($idioma->clientes()->exists()) {
            return redirect()
                ->route('admin.idiomas.index')
                ->with('error', 'No se puede eliminar este idioma porque está asignado a uno o más clientes.');
        }

        $idioma->delete();

        return redirect()
            ->route('admin.idiomas.index')
            ->with('success', 'Idioma eliminado correctamente.');
    }

    /**
     * Búsqueda AJAX para selects y autocompletado
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string'
        ]);

        $idiomas = Idioma::where('nombre', 'like', "%{$request->q}%")
            ->orWhere('codigo', 'like', "%{$request->q}%")
            ->select('id', 'nombre', 'codigo')
            ->orderBy('nombre')
            ->limit(10)
            ->get();

        return response()->json($idiomas);
    }
}
