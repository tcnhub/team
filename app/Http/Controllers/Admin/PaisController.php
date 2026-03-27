<?php

namespace App\Http\Controllers;

use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pais::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                ->orWhere('codigo_iso', 'like', "%{$request->buscar}%");
        }

        $paises = $query->orderBy('nombre')->paginate(20);

        return view('paises.index', compact('paises'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('paises.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:paises,nombre',
            'codigo_iso'  => 'nullable|string|max:3|unique:paises,codigo_iso',
        ]);

        Pais::create($validated);

        return redirect()
            ->route('admin.paises.index')
            ->with('success', 'País creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pais $pais)
    {
        return view('paises.show', compact('pais'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pais $pais)
    {
        return view('paises.edit', compact('pais'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pais $pais)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:paises,nombre,' . $pais->id,
            'codigo_iso'  => 'nullable|string|max:3|unique:paises,codigo_iso,' . $pais->id,
        ]);

        $pais->update($validated);

        return redirect()
            ->route('admin.paises.index')
            ->with('success', 'País actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pais $pais)
    {
        // Verificar si el país está siendo usado por clientes
        if ($pais->clientes()->exists()) {
            return redirect()
                ->route('admin.paises.index')
                ->with('error', 'No se puede eliminar este país porque está asignado a uno o más clientes.');
        }

        $pais->delete();

        return redirect()
            ->route('admin.paises.index')
            ->with('success', 'País eliminado correctamente.');
    }

    /**
     * Búsqueda AJAX para selects y autocompletado
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string'
        ]);

        $paises = Pais::where('nombre', 'like', "%{$request->q}%")
            ->orWhere('codigo_iso', 'like', "%{$request->q}%")
            ->select('id', 'nombre', 'codigo_iso')
            ->orderBy('nombre')
            ->limit(15)
            ->get();

        return response()->json($paises);
    }
}
