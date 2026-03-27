<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pago;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::with(['reserva.tour', 'cliente'])
            ->latest('fecha_pago');

        if ($request->filled('codigo_pago')) {
            $query->where('codigo_pago', 'like', "%{$request->codigo_pago}%");
        }

        if ($request->filled('reserva')) {
            $query->whereHas('reserva', fn ($q) =>
                $q->where('codigo_reserva', 'like', "%{$request->reserva}%")
            );
        }

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', fn ($q) =>
                $q->where('nombre_completo', 'like', "%{$request->cliente}%")
                  ->orWhere('numero_documento', 'like', "%{$request->cliente}%")
            );
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_pago', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_pago', '<=', $request->fecha_hasta);
        }

        $pagos = $query->paginate(20);

        $totalPEN = (clone $query)->where('moneda', 'PEN')->where('estado', 'confirmado')->sum('monto');
        $totalUSD = (clone $query)->where('moneda', 'USD')->where('estado', 'confirmado')->sum('monto');

        return view('admin.pagos.index', compact('pagos', 'totalPEN', 'totalUSD'));
    }

    public function create(Request $request)
    {
        $reserva = null;
        if ($request->filled('reserva_id')) {
            $reserva = Reserva::with(['cliente', 'tour'])->findOrFail($request->reserva_id);
        }

        $reservas = Reserva::with(['cliente', 'tour'])
            ->whereNotIn('estado_reserva', ['cancelada', 'reembolsada'])
            ->orderBy('codigo_reserva', 'desc')
            ->limit(200)
            ->get();

        return view('admin.pagos.create', compact('reserva', 'reservas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reserva_id'       => 'required|exists:reservas,id',
            'monto'            => 'required|numeric|min:0.01',
            'moneda'           => ['required', Rule::in(['PEN', 'USD'])],
            'tipo_pago'        => ['required', Rule::in(['inicial', 'parcial', 'final', 'proveedor', 'devolucion', 'otro'])],
            'metodo_pago'      => ['required', Rule::in(array_keys(Pago::metodosLabel()))],
            'numero_operacion' => 'nullable|string|max:60',
            'fecha_pago'       => 'required|date',
            'banco_origen'     => 'nullable|string|max:100',
            'banco_destino'    => 'nullable|string|max:100',
            'estado'           => ['required', Rule::in(['pendiente', 'confirmado', 'rechazado', 'devuelto'])],
            'notas'            => 'nullable|string',
        ]);

        $reserva = Reserva::with('cliente')->findOrFail($validated['reserva_id']);

        try {
            DB::beginTransaction();

            $pago = Pago::create([
                'reserva_id'       => $reserva->id,
                'cliente_id'       => $reserva->id_cliente,
                'registrado_por'   => Auth::guard('admin')->id(),
                'codigo_pago'      => Pago::generarCodigo(),
                'monto'            => $validated['monto'],
                'moneda'           => $validated['moneda'],
                'tipo_pago'        => $validated['tipo_pago'],
                'metodo_pago'      => $validated['metodo_pago'],
                'numero_operacion' => $validated['numero_operacion'],
                'fecha_pago'       => $validated['fecha_pago'],
                'banco_origen'     => $validated['banco_origen'],
                'banco_destino'    => $validated['banco_destino'],
                'estado'           => $validated['estado'],
                'notas'            => $validated['notas'],
            ]);

            // Recalcular monto_pagado en la reserva
            Pago::recalcularMontoPagado($reserva->id);

            // Auto-cambiar estado_reserva si corresponde
            $this->actualizarEstadoReserva($reserva->fresh());

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'ok'   => true,
                    'pago' => [
                        'id'               => $pago->id,
                        'codigo_pago'      => $pago->codigo_pago,
                        'monto'            => $pago->monto,
                        'moneda'           => $pago->moneda,
                        'metodo_texto'     => $pago->metodo_texto,
                        'tipo_texto'       => $pago->tipo_texto,
                        'fecha_pago'       => $pago->fecha_pago->format('d/m/Y'),
                        'estado'           => $pago->estado,
                        'saldo_pendiente'  => $reserva->fresh()->saldo_pendiente,
                    ],
                ]);
            }

            return redirect()
                ->route('admin.pagos.show', $pago)
                ->with('success', "Pago {$pago->codigo_pago} registrado correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->expectsJson()) {
                return response()->json(['ok' => false, 'message' => $e->getMessage()], 500);
            }

            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    public function show(Pago $pago)
    {
        $pago->load(['reserva.tour', 'reserva.cliente', 'cliente', 'registradoPor']);
        return view('admin.pagos.show', compact('pago'));
    }

    public function edit(Pago $pago)
    {
        $pago->load(['reserva.cliente', 'reserva.tour']);
        return view('admin.pagos.edit', compact('pago'));
    }

    public function update(Request $request, Pago $pago)
    {
        $validated = $request->validate([
            'monto'            => 'required|numeric|min:0.01',
            'moneda'           => ['required', Rule::in(['PEN', 'USD'])],
            'tipo_pago'        => ['required', Rule::in(['inicial', 'parcial', 'final', 'proveedor', 'devolucion', 'otro'])],
            'metodo_pago'      => ['required', Rule::in(array_keys(Pago::metodosLabel()))],
            'numero_operacion' => 'nullable|string|max:60',
            'fecha_pago'       => 'required|date',
            'banco_origen'     => 'nullable|string|max:100',
            'banco_destino'    => 'nullable|string|max:100',
            'estado'           => ['required', Rule::in(['pendiente', 'confirmado', 'rechazado', 'devuelto'])],
            'notas'            => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $pago->update($validated);
            Pago::recalcularMontoPagado($pago->reserva_id);
            $this->actualizarEstadoReserva($pago->reserva->fresh());

            DB::commit();

            return redirect()
                ->route('admin.pagos.show', $pago)
                ->with('success', 'Pago actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar el pago.');
        }
    }

    public function destroy(Pago $pago)
    {
        $reservaId = $pago->reserva_id;
        $codigo    = $pago->codigo_pago;
        $pago->delete();
        Pago::recalcularMontoPagado($reservaId);

        return redirect()
            ->route('admin.pagos.index')
            ->with('success', "Pago {$codigo} eliminado.");
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function actualizarEstadoReserva(Reserva $reserva): void
    {
        if ($reserva->saldo_pendiente <= 0 && $reserva->estado_reserva === 'confirmada') {
            $reserva->update(['estado_reserva' => 'pagada']);
        } elseif ($reserva->monto_pagado > 0 && $reserva->estado_reserva === 'pendiente') {
            $reserva->update(['estado_reserva' => 'confirmada']);
        }
    }
}
