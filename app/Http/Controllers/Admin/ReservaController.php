<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Cliente;
use App\Models\Agente;
use App\Models\Pais;
use App\Models\Tour;
use App\Models\TourAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reserva::with(['cliente', 'agente', 'tour'])
            ->latest();

        if ($request->filled('estado')) {
            $query->where('estado_reserva', $request->estado);
        }

        if ($request->filled('codigo_reserva')) {
            $query->where('codigo_reserva', 'like', "%{$request->codigo_reserva}%");
        }

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nombre_completo', 'like', "%{$request->cliente}%")
                    ->orWhere('numero_documento', 'like', "%{$request->cliente}%");
            });
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('fecha_inicio', $request->fecha_inicio);
        }

        if ($request->filled('tipo_reserva')) {
            $query->where('tipo_reserva', 'like', "%{$request->tipo_reserva}%");
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

        $agentes = Agente::where('estado', true)->get();
        $paises  = Pais::orderBy('nombre')->get();
        $tours   = Tour::where('estado', true)->orderBy('nombre_tour')->get();

        return view('admin.reservas.create', compact('clientes', 'agentes', 'paises', 'tours'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_cliente'              => 'required|exists:clientes,id',
            'id_agente'               => 'nullable|exists:agentes,id',
            'tour_id'                 => 'nullable|exists:tours,id',
            'availability_id'         => 'nullable|exists:tour_availability,id',
            'tipo_reserva'            => 'required|string|max:50',
            'descripcion_servicio'    => 'nullable|string|max:255',
            'fecha_inicio'            => 'required|date',
            'fecha_fin'               => 'nullable|date|after_or_equal:fecha_inicio',
            'num_pasajeros'           => 'required|integer|min:1',
            'num_adultos'             => 'required|integer|min:0',
            'num_ninos'               => 'required|integer|min:0',
            'num_bebes'               => 'required|integer|min:0',
            'precio_total'            => 'required|numeric|min:0',
            'descuento'               => 'nullable|numeric|min:0',
            'moneda'                  => ['required', Rule::in(['USD'])],
            'notas'                   => 'nullable|string',
            'requisitos_especiales'   => 'nullable|string',
            'fuente_reserva'          => 'nullable|string|max:50',
            // Pago inicial (opcional)
            'pago_inicial_monto'      => 'nullable|numeric|min:0.01',
            'pago_inicial_metodo'     => 'nullable|string',
            'pago_inicial_operacion'  => 'nullable|string|max:60',
            'pago_inicial_tipo'       => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $precioFinal = $validated['precio_total'] - ($validated['descuento'] ?? 0);

            $reserva = Reserva::create([
                'codigo_reserva'       => $this->generarCodigoReserva(),
                'id_cliente'           => $validated['id_cliente'],
                'id_agente'            => $validated['id_agente'] ?? null,
                'tour_id'              => $validated['tour_id'] ?? null,
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

            // ── Pago inicial ──────────────────────────────────────────────
            if (!empty($validated['pago_inicial_monto']) && $validated['pago_inicial_monto'] > 0) {
                Pago::create([
                    'reserva_id'       => $reserva->id,
                    'cliente_id'       => $reserva->id_cliente,
                    'registrado_por'   => Auth::guard('admin')->id(),
                    'codigo_pago'      => Pago::generarCodigo(),
                    'monto'            => $validated['pago_inicial_monto'],
                    'moneda'           => $validated['moneda'],
                    'tipo_pago'        => $validated['pago_inicial_tipo'] ?? 'inicial',
                    'metodo_pago'      => $validated['pago_inicial_metodo'] ?? 'efectivo',
                    'numero_operacion' => $validated['pago_inicial_operacion'] ?? null,
                    'fecha_pago'       => now()->toDateString(),
                    'estado'           => 'confirmado',
                ]);
                Pago::recalcularMontoPagado($reserva->id);
            }

            DB::commit();

            // Si tiene tour asociado, redirigir al calendario de reservas de ese tour
            if ($reserva->tour_id) {
                $anio = \Carbon\Carbon::parse($validated['fecha_inicio'])->year;
                return redirect()
                    ->route('admin.tours.reservas.calendario', ['tour' => $reserva->tour_id, 'anio' => $anio])
                    ->with('success', 'Reserva ' . $reserva->codigo_reserva . ' creada. Se muestra en el calendario del tour.');
            }

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
        $reserva->load(['cliente', 'agente', 'tour', 'pagos', 'pasajeros.cliente']);

        // Cargar availability solo si existe
        if ($reserva->availability_id) {
            $reserva->load('availability.tour');
        }

        return view('admin.reservas.show', compact('reserva'));
    }

    public function storePasajeroAjax(Request $request, Reserva $reserva)
    {
        if (! $reserva->tour_id) {
            return response()->json(['ok' => false, 'message' => 'La reserva no tiene un tour asociado.'], 422);
        }

        $validated = $this->validatePasajeroReserva($request);

        $pasajero = DB::transaction(function () use ($validated, $reserva) {
            $pasajero = Pasajero::create([
                'cliente_id' => $reserva->id_cliente,
                'reserva_id' => $reserva->id,
                'tour_id' => $reserva->tour_id,
                'tipo_pasajero' => $validated['tipo_pasajero'],
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'nombre_completo' => trim($validated['nombre'] . ' ' . $validated['apellido']),
                'tipo_documento' => $validated['tipo_documento'],
                'numero_documento' => $validated['numero_documento'],
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'whatsapp' => $validated['whatsapp'] ?? null,
                'fecha_nacimiento' => $validated['fecha_nacimiento'] ?? null,
                'genero' => $validated['genero'] ?? null,
                'activo' => true,
            ]);

            $this->actualizarResumenPasajeros($reserva, [$validated['tipo_pasajero']]);

            return $pasajero->load('tour');
        });

        return response()->json([
            'ok' => true,
            'pasajero' => [
                'id' => $pasajero->id,
                'nombre_completo' => $pasajero->nombre_completo,
                'numero_documento' => $pasajero->numero_documento,
                'tipo_pasajero' => $pasajero->tipo_pasajero,
                'tour_nombre' => $pasajero->tour?->nombre_tour,
                'show_url' => route('admin.pasajeros.show', $pasajero),
            ],
            'resumen' => $reserva->fresh(['pasajeros'])->only(['num_pasajeros', 'num_adultos', 'num_ninos']),
        ]);
    }

    public function storePasajerosBulkAjax(Request $request, Reserva $reserva)
    {
        if (! $reserva->tour_id) {
            return response()->json(['ok' => false, 'message' => 'La reserva no tiene un tour asociado.'], 422);
        }

        $validated = $request->validate([
            'pasajeros' => ['required', 'array', 'min:1'],
            'pasajeros.*.tipo_pasajero' => ['required', Rule::in(['adulto', 'estudiante', 'nino'])],
            'pasajeros.*.nombre' => ['required', 'string', 'max:255'],
            'pasajeros.*.apellido' => ['required', 'string', 'max:255'],
            'pasajeros.*.tipo_documento' => ['required', Rule::in(['passport', 'dni', 'id'])],
            'pasajeros.*.numero_documento' => ['required', 'string', 'max:255', 'distinct', 'unique:pasajeros,numero_documento'],
            'pasajeros.*.email' => ['nullable', 'email'],
            'pasajeros.*.telefono' => ['nullable', 'string', 'max:20'],
            'pasajeros.*.whatsapp' => ['nullable', 'string', 'max:20'],
            'pasajeros.*.fecha_nacimiento' => ['nullable', 'date'],
            'pasajeros.*.genero' => ['nullable', Rule::in(['male', 'female', 'other'])],
        ]);

        $creados = DB::transaction(function () use ($validated, $reserva) {
            $creados = [];
            $tipos = [];

            foreach ($validated['pasajeros'] as $item) {
                $tipos[] = $item['tipo_pasajero'];

                $creados[] = Pasajero::create([
                    'cliente_id' => $reserva->id_cliente,
                    'reserva_id' => $reserva->id,
                    'tour_id' => $reserva->tour_id,
                    'tipo_pasajero' => $item['tipo_pasajero'],
                    'nombre' => $item['nombre'],
                    'apellido' => $item['apellido'],
                    'nombre_completo' => trim($item['nombre'] . ' ' . $item['apellido']),
                    'tipo_documento' => $item['tipo_documento'],
                    'numero_documento' => $item['numero_documento'],
                    'email' => $item['email'] ?? null,
                    'telefono' => $item['telefono'] ?? null,
                    'whatsapp' => $item['whatsapp'] ?? null,
                    'fecha_nacimiento' => $item['fecha_nacimiento'] ?? null,
                    'genero' => $item['genero'] ?? null,
                    'activo' => true,
                ]);
            }

            $this->actualizarResumenPasajeros($reserva, $tipos);

            return collect($creados);
        });

        return response()->json([
            'ok' => true,
            'message' => 'Pasajeros agregados correctamente.',
            'pasajeros' => $creados->map(fn ($pasajero) => [
                'id' => $pasajero->id,
                'nombre_completo' => $pasajero->nombre_completo,
                'numero_documento' => $pasajero->numero_documento,
                'tipo_pasajero' => $pasajero->tipo_pasajero,
                'show_url' => route('admin.pasajeros.show', $pasajero),
            ]),
            'resumen' => $reserva->fresh(['pasajeros'])->only(['num_pasajeros', 'num_adultos', 'num_ninos']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reserva $reserva)
    {
        $clientes = Cliente::where('activo', true)->get();
        $agentes  = Agente::where('estado', true)->get();
        $tours    = Tour::where('estado', true)->orderBy('nombre_tour')->get();

        // Availabilities del tour seleccionado (o vacío)
        $availabilities = collect();
        if ($reserva->tour_id) {
            $availabilities = TourAvailability::with('tour')
                ->where('tour_id', $reserva->tour_id)
                ->where('disponible', true)
                ->orderBy('fecha')
                ->get();
        }

        return view('admin.reservas.edit', compact('reserva', 'clientes', 'agentes', 'tours', 'availabilities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reserva $reserva)
    {
        $validated = $request->validate([
            'id_cliente'           => 'required|exists:clientes,id',
            'id_agente'            => 'nullable|exists:agentes,id',
            'tour_id'              => 'nullable|exists:tours,id',
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
                'id_cliente'      => $validated['id_cliente'],
                'id_agente'       => $validated['id_agente'],
                'tour_id'         => $validated['tour_id'],
                'availability_id' => $validated['availability_id'],
                'estado_reserva'  => $validated['estado_reserva'],
                'fecha_inicio'    => $validated['fecha_inicio'],
                'fecha_fin'       => $validated['fecha_fin'],
                'precio_total'    => $validated['precio_total'],
                'descuento'       => $validated['descuento'] ?? 0,
                'precio_final'    => $precioFinal,
                'notas'           => $validated['notas'],
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

        $reserva->delete();

        return redirect()
            ->route('admin.reservas.index')
            ->with('success', 'Reserva eliminada correctamente');
    }

    /**
     * AJAX: crea una reserva desde el calendario de tour y devuelve JSON
     */
    public function storeAjax(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'id_cliente'    => 'required|exists:clientes,id',
            'id_agente'     => 'nullable|exists:agentes,id',
            'fecha_inicio'  => 'required|date',
            'fecha_fin'     => 'nullable|date|after_or_equal:fecha_inicio',
            'num_pasajeros' => 'required|integer|min:1',
            'num_adultos'   => 'required|integer|min:0',
            'num_ninos'     => 'required|integer|min:0',
            'num_bebes'     => 'required|integer|min:0',
            'precio_total'  => 'required|numeric|min:0',
            'descuento'     => 'nullable|numeric|min:0',
            'moneda'        => ['required', Rule::in(['USD'])],
            'notas'         => 'nullable|string',
            'fuente_reserva'=> 'nullable|string|max:50',
            // Pago inicial
            'pago_inicial_monto'     => 'nullable|numeric|min:0.01',
            'pago_inicial_metodo'    => 'nullable|string',
            'pago_inicial_operacion' => 'nullable|string|max:60',
        ]);

        try {
            DB::beginTransaction();

            $precioFinal = $validated['precio_total'] - ($validated['descuento'] ?? 0);

            $reserva = Reserva::create([
                'codigo_reserva'    => $this->generarCodigoReserva(),
                'id_cliente'        => $validated['id_cliente'],
                'id_agente'         => $validated['id_agente'] ?? null,
                'tour_id'           => $tour->id,
                'tipo_reserva'      => 'Tour',
                'descripcion_servicio' => $tour->nombre_tour . ($tour->duracion_dias ? " {$tour->duracion_dias}D" : ''),
                'fecha_inicio'      => $validated['fecha_inicio'],
                'fecha_fin'         => $validated['fecha_fin'],
                'num_pasajeros'     => $validated['num_pasajeros'],
                'num_adultos'       => $validated['num_adultos'],
                'num_ninos'         => $validated['num_ninos'],
                'num_bebes'         => $validated['num_bebes'],
                'moneda'            => $validated['moneda'],
                'precio_total'      => $validated['precio_total'],
                'descuento'         => $validated['descuento'] ?? 0,
                'precio_final'      => $precioFinal,
                'monto_pagado'      => 0,
                'notas'             => $validated['notas'],
                'fuente_reserva'    => $validated['fuente_reserva'] ?? 'Oficina',
                'estado_reserva'    => 'pendiente',
            ]);

            if (!empty($validated['pago_inicial_monto']) && $validated['pago_inicial_monto'] > 0) {
                Pago::create([
                    'reserva_id'       => $reserva->id,
                    'cliente_id'       => $reserva->id_cliente,
                    'registrado_por'   => Auth::guard('admin')->id(),
                    'codigo_pago'      => Pago::generarCodigo(),
                    'monto'            => $validated['pago_inicial_monto'],
                    'moneda'           => $validated['moneda'],
                    'tipo_pago'        => 'inicial',
                    'metodo_pago'      => $validated['pago_inicial_metodo'] ?? 'efectivo',
                    'numero_operacion' => $validated['pago_inicial_operacion'] ?? null,
                    'fecha_pago'       => $validated['fecha_inicio'],
                    'estado'           => 'confirmado',
                ]);
                Pago::recalcularMontoPagado($reserva->id);
            }

            DB::commit();

            $reserva->load('cliente');
            $fechaFin = $reserva->fecha_fin
                ?? $reserva->fecha_inicio->copy()->addDays(($tour->duracion_dias ?? 1) - 1);

            return response()->json([
                'ok'      => true,
                'reserva' => [
                    'id'             => $reserva->id,
                    'codigo_reserva' => $reserva->codigo_reserva,
                    'fecha_inicio'   => $reserva->fecha_inicio->format('Y-m-d'),
                    'fecha_fin'      => $fechaFin instanceof \Carbon\Carbon
                        ? $fechaFin->format('Y-m-d')
                        : \Carbon\Carbon::parse($fechaFin)->format('Y-m-d'),
                    'cliente_nombre' => $reserva->cliente->nombre_completo ?? $reserva->codigo_reserva,
                    'estado'         => $reserva->estado_reserva,
                    'show_url'       => route('admin.reservas.show', $reserva),
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }
    }

    /**
     * AJAX: devuelve availabilities de un tour para el select del formulario
     */
    public function availabilitiesPorTour(Request $request, Tour $tour)
    {
        $availabilities = TourAvailability::where('tour_id', $tour->id)
            ->where('disponible', true)
            ->whereDate('fecha', '>=', now()->toDateString())
            ->orderBy('fecha')
            ->limit(100)
            ->get(['id', 'fecha', 'espacios_disponibles', 'capacidad_dia']);

        return response()->json($availabilities->map(fn ($a) => [
            'id'                   => $a->id,
            'fecha'                => $a->fecha->format('Y-m-d'),
            'fecha_display'        => $a->fecha->format('d/m/Y'),
            'espacios_disponibles' => $a->espacios_disponibles,
        ]));
    }

    /**
     * Genera un código único de reserva (ej: RES-20260327-0005)
     */
    private function generarCodigoReserva(): string
    {
        $prefijo = 'RES';
        $fecha   = now()->format('Ymd');
        $ultimo  = Reserva::where('codigo_reserva', 'like', "{$prefijo}-{$fecha}-%")
            ->orderBy('id', 'desc')
            ->first();

        $numero = $ultimo ? (int) substr($ultimo->codigo_reserva, -4) + 1 : 1;

        return sprintf("%s-%s-%04d", $prefijo, $fecha, $numero);
    }

    private function validatePasajeroReserva(Request $request): array
    {
        return $request->validate([
            'tipo_pasajero' => ['required', Rule::in(['adulto', 'estudiante', 'nino'])],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'tipo_documento' => ['required', Rule::in(['passport', 'dni', 'id'])],
            'numero_documento' => ['required', 'string', 'max:255', 'unique:pasajeros,numero_documento'],
            'email' => ['nullable', 'email'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'genero' => ['nullable', Rule::in(['male', 'female', 'other'])],
        ]);
    }

    private function actualizarResumenPasajeros(Reserva $reserva, array $tipos): void
    {
        $reserva->increment('num_pasajeros', count($tipos));
        $reserva->increment('num_adultos', collect($tipos)->filter(fn ($tipo) => in_array($tipo, ['adulto', 'estudiante'], true))->count());
        $reserva->increment('num_ninos', collect($tipos)->filter(fn ($tipo) => $tipo === 'nino')->count());
    }
}
