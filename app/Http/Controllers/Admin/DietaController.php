<?php

namespace App\Http\Controllers;

use App\Models\Dieta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DietaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dieta::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                ->orWhere('descripcion', 'like', "%{$request->buscar}%");
        }

        $dietas = $query->latest()->paginate(15);

        return view('dietas.index', compact('dietas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dietas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255|unique:dietas,nombre',
            'descripcion'   => 'nullable|string|max:1000',
        ]);

        Dieta::create($validated);

        return redirect()
            ->route('admin.dietas.index')
            ->with('success', 'Dieta creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Dieta $dieta)
    {
        return view('dietas.show', compact('dieta'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dieta $dieta)
    {
        return view('dietas.edit', compact('dieta'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dieta $dieta)
    {
        $validated = $request->validate([
            'nombre'        => 'required|string|max:255|unique:dietas,nombre,' . $dieta->id,
            'descripcion'   => 'nullable|string|max:1000',
        ]);

        $dieta->update($validated);

        return redirect()
            ->route('admin.dietas.index')
            ->with('success', 'Dieta actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dieta $dieta)
    {
        // Verificar si la dieta está siendo usada por algún cliente
        if ($dieta->clientes()->exists() || $dieta->clientesPivot()->exists()) {
            return redirect()
                ->route('admin.dietas.index')
                ->with('error', 'No se puede eliminar esta dieta porque está asignada a uno o más clientes.');
        }

        $dieta->delete();

        return redirect()
            ->route('admin.dietas.index')
            ->with('success', 'Dieta eliminada correctamente.');
    }

    /**
     * Método adicional: Buscar dietas para selects (útil en AJAX)
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string'
        ]);

        $dietas = Dieta::where('nombre', 'like', "%{$request->q}%")
            ->orWhere('descripcion', 'like', "%{$request->q}%")
            ->select('id', 'nombre', 'descripcion')
            ->limit(10)
            ->get();

        return response()->json($dietas);
    }
}
