<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use App\Models\Agente;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Request $request)
    {
        $query = Tour::with('categorias')->latest();

        if ($request->filled('codigo_tour')) {
            $query->where('codigo_tour', 'like', '%' . $request->codigo_tour . '%');
        }

        if ($request->filled('nombre_tour')) {
            $query->where('nombre_tour', 'like', '%' . $request->nombre_tour . '%');
        }

        if ($request->filled('duracion_dias')) {
            $query->where('duracion_dias', $request->duracion_dias);
        }

        if ($request->filled('precio_base')) {
            $query->where('precio_base', '>=', $request->precio_base);
        }

        if ($request->filled('precio_order')) {
            $query->orderBy('precio_base', $request->precio_order === 'desc' ? 'desc' : 'asc');
        }

        if ($request->filled('nivel_dificultad')) {
            $query->where('nivel_dificultad', $request->nivel_dificultad);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === '1');
        }

        $tours = $query->paginate(15)->withQueryString();

        $clientes = Cliente::orderBy('nombre_completo')->get(['id', 'nombre_completo', 'numero_documento']);
        $agentes = Agente::where('estado', true)->orderBy('nombre_completo')->get(['id', 'nombres', 'apellidos']);

        return view('admin.tours.index', compact('tours', 'clientes', 'agentes'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', true)->orderBy('nombre')->get();
        $addons = Addon::orderBy('nombre')->get();

        return view('admin.tours.create', compact('categorias', 'addons'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTour($request);
        $validated['estado'] = $request->boolean('estado', true);
        $validated['destacado'] = $request->boolean('destacado');

        $tour = Tour::create($validated);
        $tour->categorias()->sync($request->input('categorias', []));
        $tour->addons()->sync($request->input('addons', []));

        return redirect()
            ->route('admin.tours.index')
            ->with('success', 'Tour creado correctamente.');
    }

    public function show(Tour $tour)
    {
        $tour->load(['categorias', 'addons', 'precios', 'calendarYears', 'pasajeros.cliente', 'reservas.cliente']);

        return view('admin.tours.show', compact('tour'));
    }

    public function showModalAjax(Tour $tour)
    {
        $tour->load(['categorias', 'addons']);

        return response()->json([
            'ok' => true,
            'title' => $tour->nombre_tour,
            'subtitle' => 'Tour relacionado a la reserva',
            'show_url' => route('admin.tours.show', $tour),
            'fields' => [
                ['label' => 'Codigo', 'value' => $tour->codigo_tour],
                ['label' => 'Precio base', 'value' => 'USD ' . number_format((float) $tour->precio_base, 2)],
                ['label' => 'Duracion', 'value' => trim(($tour->duracion_dias ?: 0) . ' dias / ' . ($tour->duracion_noches ?: 0) . ' noches')],
                ['label' => 'Dificultad', 'value' => $tour->nivel_dificultad ?: 'Sin registro'],
                ['label' => 'Destino', 'value' => $tour->destino_principal ?: 'Sin registro'],
                ['label' => 'Salida desde', 'value' => $tour->salida_desde ?: 'Sin registro'],
                ['label' => 'Capacidad', 'value' => ($tour->min_personas ?: 0) . ' - ' . ($tour->max_personas ?: 0) . ' personas'],
                ['label' => 'Estado', 'value' => $tour->estado ? 'Activo' : 'Inactivo'],
            ],
        ]);
    }

    public function edit(Tour $tour)
    {
        $categorias = Categoria::where('estado', true)->orderBy('nombre')->get();
        $addons = Addon::orderBy('nombre')->get();
        $tour->load(['categorias', 'addons']);

        return view('admin.tours.edit', compact('tour', 'categorias', 'addons'));
    }

    public function update(Request $request, Tour $tour)
    {
        $validated = $this->validateTour($request, $tour->id);
        $validated['estado'] = $request->boolean('estado', true);
        $validated['destacado'] = $request->boolean('destacado');

        $tour->update($validated);
        $tour->categorias()->sync($request->input('categorias', []));
        $tour->addons()->sync($request->input('addons', []));

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

    public function toggleDestacado(Tour $tour)
    {
        $tour->update(['destacado' => ! $tour->destacado]);

        return back()->with('success', 'Estado de destacado actualizado.');
    }

    public function changeStatus(Request $request, Tour $tour)
    {
        $request->validate(['estado' => ['nullable', 'boolean']]);
        $tour->update(['estado' => $request->boolean('estado')]);

        return back()->with('success', 'Estado del tour actualizado.');
    }

    public function addonsJson(Tour $tour)
    {
        return response()->json(
            $tour->addons()->orderBy('nombre')->get(['addons.id', 'addons.nombre', 'addons.descripcion', 'addons.monto'])
        );
    }

    private function validateTour(Request $request, ?int $tourId = null): array
    {
        return $request->validate([
            'codigo_tour'       => 'required|max:20|unique:tours,codigo_tour,' . $tourId,
            'nombre_tour'       => 'required|string|max:150',
            'descripcion_corta' => 'nullable|string',
            'descripcion_larga' => 'nullable|string',
            'duracion_dias'     => 'nullable|integer|min:1',
            'duracion_noches'   => 'nullable|integer|min:0',
            'nivel_dificultad'  => 'nullable|in:FÃ¡cil,Moderado,DifÃ­cil,Extremo',
            'precio_base'       => 'nullable|numeric|min:0',
            'max_personas'      => 'nullable|integer|min:1',
            'min_personas'      => 'nullable|integer|min:1',
            'salida_desde'      => 'nullable|string|max:100',
            'destino_principal' => 'nullable|string|max:100',
            'incluye'           => 'nullable|string',
            'no_incluye'        => 'nullable|string',
            'itinerario'        => 'nullable|array',
            'categorias'        => 'nullable|array',
            'categorias.*'      => 'exists:categorias,id',
            'addons'            => 'nullable|array',
            'addons.*'          => 'exists:addons,id',
            'estado'            => 'nullable|boolean',
            'destacado'         => 'nullable|boolean',
        ]);
    }
}
