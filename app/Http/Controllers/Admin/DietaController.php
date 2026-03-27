<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dieta;
use Illuminate\Http\Request;

class DietaController extends Controller
{
    public function index(Request $request)
    {
        $query = Dieta::query();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', "%{$request->buscar}%")
                ->orWhere('descripcion', 'like', "%{$request->buscar}%");
        }

        $dietas = $query->latest()->paginate(15);

        return view('admin.dietas.index', compact('dietas'));
    }

    public function create()
    {
        return view('admin.dietas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:dietas,nombre',
            'descripcion' => 'nullable|string|max:1000',
        ]);

        Dieta::create($validated);

        return redirect()
            ->route('admin.dietas.index')
            ->with('success', 'Dieta creada correctamente.');
    }

    public function show(Dieta $dieta)
    {
        return view('admin.dietas.show', compact('dieta'));
    }

    public function edit(Dieta $dieta)
    {
        return view('admin.dietas.edit', compact('dieta'));
    }

    public function update(Request $request, Dieta $dieta)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:255|unique:dietas,nombre,' . $dieta->id,
            'descripcion' => 'nullable|string|max:1000',
        ]);

        $dieta->update($validated);

        return redirect()
            ->route('admin.dietas.index')
            ->with('success', 'Dieta actualizada correctamente.');
    }

    public function destroy(Dieta $dieta)
    {
        if ($dieta->clientes()->exists() || $dieta->clientesPivot()->exists()) {
            return back()->with('error', 'No se puede eliminar esta dieta porque está asignada a uno o más clientes.');
        }

        $dieta->delete();

        return redirect()
            ->route('admin.dietas.index')
            ->with('success', 'Dieta eliminada correctamente.');
    }

    public function buscar(Request $request)
    {
        $dietas = Dieta::where('nombre', 'like', "%{$request->q}%")
            ->orWhere('descripcion', 'like', "%{$request->q}%")
            ->select('id', 'nombre', 'descripcion')
            ->limit(10)
            ->get();

        return response()->json($dietas);
    }
}
