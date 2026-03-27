<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'codigo_proveedor',
        'razon_social',
        'nombre_comercial',
        'tipo_documento',
        'numero_documento',
        'tipo_proveedor_id',
        'direccion',
        'distrito',
        'ciudad',
        'telefono_principal',
        'telefono_secundario',
        'email_principal',
        'email_contabilidad',
        'pagina_web',
        'contacto_nombre',
        'contacto_cargo',
        'contacto_celular',
        'estado',
        'notas',
        'calificacion',
        'ruc_vigente',
        'mincetour_calificado',
        'cuenta_bancaria',
        'banco',
        'moneda_principal',
        'condiciones_pago',
        'descuento_negociado',
        'logo_url',
        'capacidad_maxima',
        'horario_atencion',
        'coordenadas_gps',
        'idiomas_atendidos',
        'certificaciones',
    ];

    protected $casts = [
        'calificacion'          => 'decimal:1',
        'descuento_negociado'   => 'decimal:2',
        'ruc_vigente'           => 'boolean',
        'mincetour_calificado'  => 'boolean',
        'capacidad_maxima'      => 'integer',
        'fecha_registro'        => 'datetime',
        'ultima_actualizacion'  => 'datetime',
    ];

    /**
     * Relación con Tipo de Proveedor (Categoría)
     */
    public function tipoProveedor()
    {
        return $this->belongsTo(TipoProveedor::class, 'tipo_proveedor_id');
    }

    /**
     * Scopes útiles
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'Activo');
    }

    public function scopePorTipo($query, $tipoProveedorId)
    {
        return $query->where('tipo_proveedor_id', $tipoProveedorId);
    }

    public function scopeEnCiudad($query, $ciudad)
    {
        return $query->where('ciudad', $ciudad);
    }

    public function scopeConBuenaCalificacion($query)
    {
        return $query->where('calificacion', '>=', 4.0);
    }

    /**
     * Accesorios (Accessors)
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombre_comercial ?? $this->razon_social;
    }

    public function getTelefonoAttribute()
    {
        return $this->telefono_principal;
    }

    public function getEstaActivoAttribute()
    {
        return $this->estado === 'Activo';
    }

    /**
     * Mutators (si necesitas)
     */
    public function setCodigoProveedorAttribute($value)
    {
        $this->attributes['codigo_proveedor'] = strtoupper(trim($value));
    }
}
