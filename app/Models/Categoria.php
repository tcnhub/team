<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'color',
        'icono',
        'estado',
    ];

    /**
     * Casts para tipos de datos
     */
    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación Many-to-Many con Tours
     * Un categoría puede tener muchos tours
     */
    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'categoria_tour');
    }

    /**
     * Scopes (consultas reutilizables)
     */

    /**
     * Solo categorías activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', true);
    }

    /**
     * Ordenadas alfabéticamente por nombre
     */
    public function scopeOrdenadas($query)
    {
        return $query->orderBy('nombre');
    }

    /**
     * Accesor (opcional) - Devuelve el nombre con color para vistas
     */
    public function getNombreConColorAttribute()
    {
        if ($this->color) {
            return "<span style='color: {$this->color}'>{$this->nombre}</span>";
        }
        return $this->nombre;
    }

    public function setEstadoAttribute($value): void
    {
        $this->attributes['estado'] = in_array($value, [true, 1, '1', 'activo', 'Activo'], true) ? 1 : 0;
    }
}
