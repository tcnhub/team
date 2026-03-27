<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'grupos';

    protected $fillable = [
        'codigo_grupo',
        'nombre_grupo',
        'reserva_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'num_pasajeros',
        'num_adultos',
        'num_ninos',
        'num_bebes',
        'moneda',
        'precio_total',
        'monto_pagado',
        'destino_principal',
        'itinerario_tipo',
        'guia_principal_id',
        'chofer_principal_id',
        'notas_operativas',
        'observaciones',
        'creado_por',
        'fecha_creacion_biblia',
    ];

    protected $casts = [
        'fecha_inicio'          => 'date',
        'fecha_fin'             => 'date',
        'fecha_creacion_biblia' => 'datetime',
        'precio_total'          => 'decimal:2',
        'monto_pagado'          => 'decimal:2',
        'saldo_pendiente'       => 'decimal:2',
        'num_pasajeros'         => 'integer',
        'num_adultos'           => 'integer',
        'num_ninos'             => 'integer',
        'num_bebes'             => 'integer',
    ];

    /**
     * ========================================
     * RELACIONES
     * ========================================
     */

    /**
     * Relación con la Reserva
     */
    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    /**
     * Relación con Guía Principal (Proveedor)
     */
    public function guiaPrincipal()
    {
        return $this->belongsTo(Proveedor::class, 'guia_principal_id');
    }

    /**
     * Relación con Chofer Principal (Proveedor)
     */
    public function choferPrincipal()
    {
        return $this->belongsTo(Proveedor::class, 'chofer_principal_id');
    }

    /**
     * Relación con Pagos (un grupo puede tener varios pagos)
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'grupo_id');
    }

    /**
     * Relación con Pasajeros (si más adelante creas una tabla intermedia)
     */
    // public function pasajeros()
    // {
    //     return $this->hasMany(Pasajero::class);
    // }

    /**
     * Relación con Usuario que creó la Biblia
     */
    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * ========================================
     * SCOPES
     * ========================================
     */

    public function scopeActivos($query)
    {
        return $query->where('estado', '!=', 'cancelado');
    }

    public function scopeEnEjecucion($query)
    {
        return $query->where('estado', 'en_ejecucion');
    }

    public function scopeConfirmados($query)
    {
        return $query->where('estado', 'confirmado');
    }

    public function scopePorFechaInicio($query, $fecha)
    {
        return $query->where('fecha_inicio', $fecha);
    }

    public function scopeProximos($query, $dias = 30)
    {
        return $query->where('fecha_inicio', '>=', now())
            ->where('fecha_inicio', '<=', now()->addDays($dias));
    }

    /**
     * ========================================
     * ACCESORES (Accessors)
     * ========================================
     */

    /**
     * Nombre legible del estado
     */
    public function getEstadoTextoAttribute()
    {
        return match($this->estado) {
            'pendiente'     => 'Pendiente',
            'confirmado'    => 'Confirmado',
            'en_ejecucion'  => 'En Ejecución',
            'completado'    => 'Completado',
            'cancelado'     => 'Cancelado',
            default         => ucfirst($this->estado ?? 'Desconocido')
        };
    }

    /**
     * Porcentaje de pago del grupo
     */
    public function getPorcentajePagadoAttribute()
    {
        if ($this->precio_total <= 0) return 0;
        return round(($this->monto_pagado / $this->precio_total) * 100, 2);
    }

    /**
     * Determina si el grupo está completamente pagado
     */
    public function getEstaPagadoAttribute()
    {
        return $this->saldo_pendiente <= 0;
    }

    /**
     * Duración del tour en días
     */
    public function getDuracionDiasAttribute()
    {
        if (!$this->fecha_fin) return null;
        return $this->fecha_inicio->diffInDays($this->fecha_fin) + 1;
    }

    /**
     * Nombre corto para mostrar en listas
     */
    public function getNombreCortoAttribute()
    {
        return $this->codigo_grupo . ' - ' . $this->nombre_grupo;
    }
}
