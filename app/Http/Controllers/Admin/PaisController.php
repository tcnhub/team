<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pais;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function index(Request $request)
    {
        $query = Pais::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                ->orWhere('codigo_iso', 'like', "%{$request->buscar}%");
        }

        $paises = $query->orderBy('nombre')->paginate(20);

        return view('admin.paises.index', compact('paises'));
    }

    public function create()
    {
        return view('admin.paises.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'     => 'required|string|max:255|unique:paises,nombre',
            'codigo_iso' => 'nullable|string|max:3|unique:paises,codigo_iso',
        ]);

        Pais::create($validated);

        return redirect()
            ->route('admin.paises.index')
            ->with('success', 'País creado correctamente.');
    }

    public function show(Pais $pais)
    {
        return view('admin.paises.show', compact('pais'));
    }

    public function edit(Pais $pais)
    {
        return view('admin.paises.edit', compact('pais'));
    }

    public function update(Request $request, Pais $pais)
    {
        $validated = $request->validate([
            'nombre'     => 'required|string|max:255|unique:paises,nombre,' . $pais->id,
            'codigo_iso' => 'nullable|string|max:3|unique:paises,codigo_iso,' . $pais->id,
        ]);

        $pais->update($validated);

        return redirect()
            ->route('admin.paises.index')
            ->with('success', 'País actualizado correctamente.');
    }

    public function destroy(Pais $pais)
    {
        if ($pais->clientes()->exists()) {
            return back()->with('error', 'No se puede eliminar este país porque está asignado a uno o más clientes.');
        }

        $pais->delete();

        return redirect()
            ->route('admin.paises.index')
            ->with('success', 'País eliminado correctamente.');
    }

    public function buscar(Request $request)
    {
        $paises = Pais::where('nombre', 'like', "%{$request->q}%")
            ->orWhere('codigo_iso', 'like', "%{$request->q}%")
            ->select('id', 'nombre', 'codigo_iso')
            ->orderBy('nombre')
            ->limit(15)
            ->get();

        return response()->json($paises);
    }
}
