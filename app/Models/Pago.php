<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'reserva_id',
        'cliente_id',
        'grupo_id',
        'proveedor_id',
        'registrado_por',
        'codigo_pago',
        'monto',
        'moneda',
        'tipo_pago',
        'metodo_pago',
        'numero_operacion',
        'fecha_pago',
        'banco_origen',
        'banco_destino',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_pago'      => 'date',
        'fecha_registro'  => 'datetime',
        'monto'           => 'decimal:2',
    ];

    // ── Etiquetas legibles ──────────────────────────────────────────────

    public static function metodosLabel(): array
    {
        return [
            'transferencia_bancaria' => 'Transferencia Bancaria',
            'yape'                   => 'Yape',
            'plin'                   => 'Plin',
            'efectivo'               => 'Efectivo',
            'tarjeta_credito'        => 'Tarjeta de Crédito',
            'tarjeta_debito'         => 'Tarjeta de Débito',
            'paypal'                 => 'PayPal',
            'otro'                   => 'Otro',
        ];
    }

    public static function tiposLabel(): array
    {
        return [
            'inicial'    => 'Inicial / Seña',
            'parcial'    => 'Parcial',
            'final'      => 'Final',
            'proveedor'  => 'Proveedor',
            'devolucion' => 'Devolución',
            'otro'       => 'Otro',
        ];
    }

    public function getMetodoTextoAttribute(): string
    {
        return static::metodosLabel()[$this->metodo_pago] ?? $this->metodo_pago;
    }

    public function getTipoTextoAttribute(): string
    {
        return static::tiposLabel()[$this->tipo_pago] ?? $this->tipo_pago;
    }

    // ── Relaciones ──────────────────────────────────────────────────────

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(\App\Models\Admin::class, 'registrado_por');
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    /**
     * Genera el código único de pago: PAGO-YYYYMM-NNNN
     */
    public static function generarCodigo(): string
    {
        $prefijo = 'PAGO-' . now()->format('Ym');
        $ultimo  = static::where('codigo_pago', 'like', "{$prefijo}-%")
            ->orderBy('id', 'desc')
            ->first();

        $numero = $ultimo ? (int) substr($ultimo->codigo_pago, -4) + 1 : 1;

        return sprintf('%s-%04d', $prefijo, $numero);
    }

    /**
     * Recalcula monto_pagado de la reserva sumando los pagos confirmados.
     */
    public static function recalcularMontoPagado(int $reservaId): void
    {
        $total = static::where('reserva_id', $reservaId)
            ->where('estado', 'confirmado')
            ->whereNotIn('tipo_pago', ['proveedor', 'devolucion'])
            ->sum('monto');

        Reserva::where('id', $reservaId)->update(['monto_pagado' => $total]);
    }
}
