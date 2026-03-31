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
        'estado'            => 'boolean',
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

    public function priceSections()
    {
        return $this->hasMany(TourPriceSection::class)->orderBy('anio')->orderBy('orden')->orderBy('id');
    }

    /**
     * Relación con los años de calendario
     */
    public function calendarYears()
    {
        return $this->hasMany(TourCalendarYear::class, 'tour_id');
    }

    /**
     * Relación con la disponibilidad diaria
     */
    public function availability()
    {
        return $this->hasMany(TourAvailability::class, 'tour_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'tour_id');
    }

    public function pasajeros()
    {
        return $this->hasMany(Pasajero::class);
    }

    public function addons()
    {
        return $this->belongsToMany(Addon::class, 'addon_tour')->withTimestamps();
    }

    /**
     * Accessor: capacidad diaria por defecto (usa max_personas)
     */
    public function getCapacidadDiariaAttribute(): int
    {
        return $this->max_personas ?? 300;
    }

    /**
     * Accessor: capacidad máxima (usa max_personas)
     */
    public function getCapacidadMaximaAttribute(): int
    {
        return $this->max_personas ?? 300;
    }

    /**
     * Accessor: nombre (alias de nombre_tour para compatibilidad)
     */
    public function getNombreAttribute(): string
    {
        return $this->nombre_tour ?? '';
    }

    public function setEstadoAttribute($value): void
    {
        $this->attributes['estado'] = in_array($value, [true, 1, '1', 'activo', 'Activo'], true) ? 1 : 0;
    }

    /**
     * Scopes útiles
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }
}
