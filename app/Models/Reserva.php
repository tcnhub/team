<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserva extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservas';

    protected $primaryKey = 'id';

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'codigo_reserva',
        'estado_reserva',
        'id_cliente',
        'id_agente',
        'tipo_reserva',
        'descripcion_servicio',
        'fecha_inicio',
        'fecha_fin',
        'origen',
        'destino',
        'num_pasajeros',
        'num_adultos',
        'num_ninos',
        'num_bebes',
        'moneda',
        'precio_total',
        'descuento',
        'precio_final',
        'monto_pagado',
        'notas',
        'requisitos_especiales',
        'fuente_reserva',
        'fecha_cancelacion',
        'motivo_cancelacion',
    ];

    /**
     * Campos que deben ser casteados a tipos específicos
     */
    protected $casts = [
        'fecha_reserva'       => 'datetime',
        'fecha_inicio'        => 'date',
        'fecha_fin'           => 'date',
        'fecha_cancelacion'   => 'datetime',
        'precio_total'        => 'decimal:2',
        'descuento'           => 'decimal:2',
        'precio_final'        => 'decimal:2',
        'monto_pagado'        => 'decimal:2',
        'saldo_pendiente'     => 'decimal:2',
        'num_pasajeros'       => 'integer',
        'num_adultos'         => 'integer',
        'num_ninos'           => 'integer',
        'num_bebes'           => 'integer',
    ];

    /**
     * Atributos que se calculan automáticamente (no se guardan en BD)
     */
    protected $appends = [
        // Puedes agregar accesorios aquí si lo deseas
    ];

    /**
     * ========================================
     * RELACIONES
     * ========================================
     */

    /**
     * Relación con Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    /**
     * Relación con Agente (vendedor)
     */
    public function agente()
    {
        return $this->belongsTo(Agente::class, 'id_agente');
    }

    /**
     * Relación con Pagos (una reserva puede tener muchos pagos)
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'reserva_id');
    }

    /**
     * Relación con Grupo/Biblia (opcional)
     * Si más adelante vinculas la reserva con una Biblia/Grupo
     */
    public function grupo()
    {
        return $this->hasOne(Grupo::class, 'reserva_id'); // o belongsTo si es al revés
    }

    /**
     * ========================================
     * SCOPES (consultas frecuentes)
     * ========================================
     */

    public function scopeConfirmadas($query)
    {
        return $query->where('estado_reserva', 'confirmada');
    }

    public function scopePendientes($query)
    {
        return $query->where('estado_reserva', 'pendiente');
    }

    public function scopePagadas($query)
    {
        return $query->where('estado_reserva', 'pagada');
    }

    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('id_cliente', $clienteId);
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_inicio', [$inicio, $fin]);
    }

    /**
     * ========================================
     * ACCESORES (Accessors)
     * ========================================
     */

    /**
     * Nombre completo del cliente (acceso fácil)
     */
    public function getClienteNombreAttribute()
    {
        return $this->cliente?->nombre_completo ?? 'Sin cliente';
    }

    /**
     * Estado en formato legible
     */
    public function getEstadoTextoAttribute()
    {
        return match($this->estado_reserva) {
            'pendiente'    => 'Pendiente',
            'confirmada'   => 'Confirmada',
            'pagada'       => 'Pagada',
            'cancelada'    => 'Cancelada',
            'reembolsada'  => 'Reembolsada',
            'completada'   => 'Completada',
            default        => ucfirst($this->estado_reserva)
        };
    }

    /**
     * Porcentaje de pago realizado
     */
    public function getPorcentajePagadoAttribute()
    {
        if ($this->precio_final == 0) return 0;
        return round(($this->monto_pagado / $this->precio_final) * 100, 2);
    }

    /**
     * Determina si la reserva está completamente pagada
     */
    public function getEstaPagadaAttribute()
    {
        return $this->saldo_pendiente <= 0;
    }
}
