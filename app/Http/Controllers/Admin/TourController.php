<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Agente;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TourController extends Controller
{
    public function index()
    {
        $tours = Tour::with('categorias')
            ->latest()
            ->paginate(15);

        $clientes = Cliente::orderBy('nombre_completo')->get(['id', 'nombre_completo', 'numero_documento']);
        $agentes  = Agente::where('activo', true)->orderBy('nombre_completo')->get(['id', 'nombre_completo']);

        return view('admin.tours.index', compact('tours', 'clientes', 'agentes'));
    }

    public function create()
    {
        $categorias = Categoria::where('activo', true)->get();
        return view('admin.tours.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_tour'        => 'required|unique:tours,codigo_tour|max:20',
            'nombre_tour'        => 'required|string|max:150',
            'descripcion_corta'  => 'nullable|string',
            'descripcion_larga'  => 'nullable|string',
            'duracion_dias'      => 'nullable|integer|min:1',
            'duracion_noches'    => 'nullable|integer|min:0',
            'nivel_dificultad'   => 'nullable|in:Fácil,Moderado,Difícil,Extremo',
            'precio_base'        => 'nullable|numeric|min:0',
            'max_personas'       => 'nullable|integer|min:1',
            'min_personas'       => 'nullable|integer|min:1',
            'salida_desde'       => 'nullable|string|max:100',
            'destino_principal'  => 'nullable|string|max:100',
            'incluye'            => 'nullable|string',
            'no_incluye'         => 'nullable|string',
            'itinerario'         => 'nullable|array',
            'categorias'         => 'nullable|array',
            'categorias.*'       => 'exists:categorias,id',
            'estado'             => 'required|in:Activo,Inactivo,Agotado,Cancelado',
            'destacado'          => 'boolean',
        ]);

        $tour = Tour::create($validated);

        // Asignar categorías
        if ($request->has('categorias')) {
            $tour->categorias()->sync($request->categorias);
        }

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour creado correctamente.');
    }

    public function show(Tour $tour)
    {
        $tour->load(['categorias', 'precios', 'calendarYears']);
        return view('admin.tours.show', compact('tour'));
    }

    public function edit(Tour $tour)
    {
        $categorias = Categoria::where('activo', true)->get();
        $tour->load('categorias');
        return view('admin.tours.edit', compact('tour', 'categorias'));
    }

    public function update(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'codigo_tour'        => 'required|unique:tours,codigo_tour,' . $tour->id . '|max:20',
            'nombre_tour'        => 'required|string|max:150',
            'descripcion_corta'  => 'nullable|string',
            'descripcion_larga'  => 'nullable|string',
            'duracion_dias'      => 'nullable|integer|min:1',
            'duracion_noches'    => 'nullable|integer|min:0',
            'nivel_dificultad'   => 'nullable|in:Fácil,Moderado,Difícil,Extremo',
            'precio_base'        => 'nullable|numeric|min:0',
            'max_personas'       => 'nullable|integer|min:1',
            'min_personas'       => 'nullable|integer|min:1',
            'salida_desde'       => 'nullable|string|max:100',
            'destino_principal'  => 'nullable|string|max:100',
            'incluye'            => 'nullable|string',
            'no_incluye'         => 'nullable|string',
            'itinerario'         => 'nullable|array',
            'categorias'         => 'nullable|array',
            'categorias.*'       => 'exists:categorias,id',
            'estado'             => 'required|in:Activo,Inactivo,Agotado,Cancelado',
            'destacado'          => 'boolean',
        ]);

        $tour->update($validated);

        if ($request->has('categorias')) {
            $tour->categorias()->sync($request->categorias);
        }

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour actualizado correctamente.');
    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour eliminado correctamente.');
    }

    // Métodos adicionales útiles
    public function toggleDestacado(Tour $tour)
    {
        $tour->update(['destacado' => !$tour->destacado]);
        return back()->with('success', 'Estado de destacado actualizado.');
    }

    public function changeStatus(Request $request, Tour $tour)
    {
        $request->validate(['estado' => 'required|in:Activo,Inactivo,Agotado,Cancelado']);
        $tour->update(['estado' => $request->estado]);
        return back()->with('success', 'Estado del tour actualizado.');
    }
}
