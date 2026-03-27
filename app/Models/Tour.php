<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    use HasFactory;

    protected $table = 'tours';

    protected $fillable = [
        'codigo_tour',
        'nombre_tour',
        'descripcion_corta',
        'descripcion_larga',
        'duracion_dias',
        'duracion_noches',
        'nivel_dificultad',
        'precio_base',
        'moneda',
        'max_personas',
        'min_personas',
        'salida_desde',
        'destino_principal',
        'incluye',
        'no_incluye',
        'itinerario',
        'galeria_imagenes',
        'estado',
        'destacado',
    ];

    protected $casts = [
        'itinerario'        => 'array',
        'galeria_imagenes'  => 'array',
        'destacado'         => 'boolean',
        'precio_base'       => 'decimal:2',
    ];

    /**
     * Relación Many-to-Many con Categorías
     */
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categoria_tour');
    }

    /**
     * Relación con los diferentes niveles de precios
     */
    public function precios()
    {
        return $this->hasMany(TourPrecio::class);
    }

    /**
     * Scopes útiles
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'Activo');
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }
}
