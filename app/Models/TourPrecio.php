<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPrecio extends Model
{
    use HasFactory;

    protected $table = 'tour_precios';

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'tour_id',
        'etiqueta',
        'precio',
        'moneda',
        'descripcion',
        'min_personas',
        'max_personas',
        'es_predeterminado',
    ];

    /**
     * Casts para tipos de datos
     */
    protected $casts = [
        'precio'            => 'decimal:2',
        'min_personas'      => 'integer',
        'max_personas'      => 'integer',
        'es_predeterminado' => 'boolean',
    ];

    /**
     * Relación con el Tour (Many to One)
     * Un precio pertenece a un solo tour
     */
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    /**
     * Scopes útiles
     */

    /**
     * Precios predeterminados (los que se muestran por defecto)
     */
    public function scopePredeterminados($query)
    {
        return $query->where('es_predeterminado', true);
    }

    /**
     * Ordenar por precio ascendente
     */
    public function scopeOrdenadosPorPrecio($query)
    {
        return $query->orderBy('precio', 'asc');
    }

    /**
     * Filtrar por moneda
     */
    public function scopePorMoneda($query, string $moneda = 'PEN')
    {
        return $query->where('moneda', $moneda);
    }

    /**
     * Accesor: Devuelve el precio formateado con moneda
     * Ejemplo: S/ 120.00 o $ 120.00
     */
    public function getPrecioFormateadoAttribute()
    {
        $simbolo = $this->moneda === 'USD' ? '$' : 'S/';
        return $simbolo . ' ' . number_format($this->precio, 2);
    }

    /**
     * Accesor: Devuelve la etiqueta + descripción (útil para mostrar)
     */
    public function getNombreCompletoAttribute()
    {
        if ($this->descripcion) {
            return "{$this->etiqueta} - {$this->descripcion}";
        }
        return $this->etiqueta;
    }
}
