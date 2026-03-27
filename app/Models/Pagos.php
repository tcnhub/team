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
        'registrado_por',
        'notas',
    ];

    protected $casts = [
        'monto'          => 'decimal:2',
        'fecha_pago'     => 'date',
        'fecha_registro' => 'datetime',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
    ];

    /**
     * ========================================
     * RELACIONES
     * ========================================
     */

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * ========================================
     * SCOPES
     * ========================================
     */

    public function scopeConfirmados($query)
    {
        return $query->where('estado', 'confirmado');
    }

    public function scopePorReserva($query, $reservaId)
    {
        return $query->where('reserva_id', $reservaId);
    }

    public function scopeDeTipo($query, $tipo)
    {
        return $query->where('tipo_pago', $tipo);
    }

    /**
     * ========================================
     * ACCESORES
     * ========================================
     */

    public function getMontoFormateadoAttribute()
    {
        return number_format($this->monto, 2) . ' ' . $this->moneda;
    }

    public function getEsPagoAProveedorAttribute()
    {
        return $this->tipo_pago === 'proveedor';
    }

    public function getEsReembolsoAttribute()
    {
        return $this->tipo_pago === 'devolucion';
    }
}
