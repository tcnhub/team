<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Agente;
use App\Models\TourAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reserva::with(['cliente', 'agente', 'availability.tour'])
            ->latest();

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado_reserva', $request->estado);
        }

        if ($request->filled('codigo_reserva')) {
            $query->where('codigo_reserva', 'like', "%{$request->codigo_reserva}%");
        }

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function($q) use ($request) {
                $q->where('nombre_completo', 'like', "%{$request->cliente}%")
                    ->orWhere('numero_documento', 'like', "%{$request->cliente}%");
            });
        }

        $reservas = $query->paginate(15);

        return view('admin.reservas.index', compact('reservas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::where('activo', true)
            ->orderBy('nombre_completo')
            ->get();

        $agentes = Agente::where('estado', 'activo')->get();
        $availabilities = TourAvailability::with('tour')
            ->where('disponible', true)
            ->whereDate('fecha', '>=', now()->toDateString())
            ->limit(50)
            ->get();

        return view('admin.reservas.create', compact('clientes', 'agentes', 'availabilities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente'           => 'required|exists:clientes,id',
            'id_agente'            => 'nullable|exists:agentes,id',
            'availability_id'      => 'nullable|exists:tour_availability,id',
            'tipo_reserva'         => 'required|string|max:50',
            'descripcion_servicio' => 'nullable|string|max:255',
            'fecha_inicio'         => 'required|date',
            'fecha_fin'            => 'nullable|date|after_or_equal:fecha_inicio',
            'num_pasajeros'        => 'required|integer|min:1',
            'num_adultos'          => 'required|integer|min:0',
            'num_ninos'            => 'required|integer|min:0',
            'num_bebes'            => 'required|integer|min:0',
            'precio_total'         => 'required|numeric|min:0',
            'descuento'            => 'nullable|numeric|min:0',
            'moneda'               => ['required', Rule::in(['PEN', 'USD'])],
            'notas'                => 'nullable|string',
            'requisitos_especiales'=> 'nullable|string',
            'fuente_reserva'       => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // Calcular precio_final
            $precioFinal = $validated['precio_total'] - ($validated['descuento'] ?? 0);

            $reserva = Reserva::create([
                'codigo_reserva'       => $this->generarCodigoReserva(),
                'id_cliente'           => $validated['id_cliente'],
                'id_agente'            => $validated['id_agente'] ?? null,
                'availability_id'      => $validated['availability_id'] ?? null,
                'tipo_reserva'         => $validated['tipo_reserva'],
                'descripcion_servicio' => $validated['descripcion_servicio'],
                'fecha_inicio'         => $validated['fecha_inicio'],
                'fecha_fin'            => $validated['fecha_fin'],
                'num_pasajeros'        => $validated['num_pasajeros'],
                'num_adultos'          => $validated['num_adultos'],
                'num_ninos'            => $validated['num_ninos'],
                'num_bebes'            => $validated['num_bebes'],
                'moneda'               => $validated['moneda'],
                'precio_total'         => $validated['precio_total'],
                'descuento'            => $validated['descuento'] ?? 0,
                'precio_final'         => $precioFinal,
                'monto_pagado'         => 0,
                'notas'                => $validated['notas'],
                'requisitos_especiales'=> $validated['requisitos_especiales'],
                'fuente_reserva'       => $validated['fuente_reserva'] ?? 'Oficina',
                'estado_reserva'       => 'pendiente',
            ]);

            DB::commit();

            return redirect()
                ->route('admin.reservas.show', $reserva)
                ->with('success', 'Reserva creada correctamente. Código: ' . $reserva->codigo_reserva);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear la reserva: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserva $reserva)
    {
        $reserva->load(['cliente', 'agente', 'availability.tour', 'pagos']);
        return view('admin.reservas.show', compact('reserva'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        $clientes = Cliente::where('activo', true)->get();
        $agentes = Agente::where('estado', 'activo')->get();
        $availabilities = TourAvailability::with('tour')->where('disponible', true)->get();

        return view('admin.reservas.edit', compact('reserva', 'clientes', 'agentes', 'availabilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        $validated = $request->validate([
            'id_cliente'           => 'required|exists:clientes,id',
            'id_agente'            => 'nullable|exists:agentes,id',
            'availability_id'      => 'nullable|exists:tour_availability,id',
            'estado_reserva'       => ['required', Rule::in(['pendiente', 'confirmada', 'pagada', 'cancelada', 'reembolsada', 'completada'])],
            'fecha_inicio'         => 'required|date',
            'fecha_fin'            => 'nullable|date|after_or_equal:fecha_inicio',
            'precio_total'         => 'required|numeric|min:0',
            'descuento'            => 'nullable|numeric|min:0',
            'notas'                => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $precioFinal = $validated['precio_total'] - ($validated['descuento'] ?? 0);

            $reserva->update([
                'id_cliente'           => $validated['id_cliente'],
                'id_agente'            => $validated['id_agente'],
                'availability_id'      => $validated['availability_id'],
                'estado_reserva'       => $validated['estado_reserva'],
                'fecha_inicio'         => $validated['fecha_inicio'],
                'fecha_fin'            => $validated['fecha_fin'],
                'precio_total'         => $validated['precio_total'],
                'descuento'            => $validated['descuento'] ?? 0,
                'precio_final'         => $precioFinal,
                'notas'                => $validated['notas'],
            ]);

            DB::commit();

            return redirect()
                ->route('admin.reservas.show', $reserva)
                ->with('success', 'Reserva actualizada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar la reserva');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reserva $reserva)
    {
        if ($reserva->estado_reserva === 'pagada' || $reserva->monto_pagado > 0) {
            return back()->with('error', 'No se puede eliminar una reserva con pagos realizados.');
        }

        $reserva->delete(); // Soft delete

        return redirect()
            ->route('admin.reservas.index')
            ->with('success', 'Reserva eliminada correctamente');
    }

    /**
     * Genera un código único de reserva (ej: RES-20260327-0005)
     */
    private function generarCodigoReserva(): string
    {
        $prefijo = 'RES';
        $fecha = now()->format('Ymd');
        $ultimo = Reserva::where('codigo_reserva', 'like', "{$prefijo}-{$fecha}-%")
            ->orderBy('id', 'desc')
            ->first();

        $numero = $ultimo ? (int) substr($ultimo->codigo_reserva, -4) + 1 : 1;

        return sprintf("%s-%s-%04d", $prefijo, $fecha, $numero);
    }
}
