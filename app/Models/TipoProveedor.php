<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProveedor extends Model
{
    use HasFactory;

    protected $table = 'tipo_proveedores';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'icono',
        'orden',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'orden'  => 'integer',
    ];

    /**
     * Relación con Proveedores
     */
    public function proveedores()
    {
        return $this->hasMany(Proveedor::class, 'tipo_proveedor_id');
    }

    /**
     * Scope para solo tipos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope ordenados por orden de visualización
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden')->orderBy('nombre');
    }
}
