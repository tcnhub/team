<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourAvailability extends Model
{
    use HasFactory;

    protected $table = 'tour_availability';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tour_id',
        'calendar_year_id',
        'fecha',
        'capacidad_dia',
        'espacios_usados',
        'espacios_bloqueados',
        // No incluimos las columnas generadas (storedAs):
        // espacios_disponibles y disponible
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha'               => 'date',
        'capacidad_dia'       => 'integer',
        'espacios_usados'     => 'integer',
        'espacios_bloqueados' => 'integer',
        'espacios_disponibles'=> 'integer',   // columna generada
        'disponible'          => 'boolean',   // columna generada
    ];

    /**
     * Las columnas generadas por la base de datos (storedAs)
     * no deben ser llenadas manualmente.
     */
    protected $guarded = [
        'espacios_disponibles',
        'disponible',
        'updated_at',
    ];

    /**
     * Relaciones
     */

    /**
     * Un registro de disponibilidad pertenece a un Tour
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    /**
     * Un registro de disponibilidad pertenece a un año del calendario del tour
     */
    public function calendarYear(): BelongsTo
    {
        return $this->belongsTo(TourCalendarYear::class, 'calendar_year_id');
    }

    /**
     * Scope para obtener solo las fechas disponibles
     */
    public function scopeDisponible($query)
    {
        return $query->where('disponible', true);
    }

    /**
     * Scope para obtener disponibilidades a partir de una fecha
     */
    public function scopeDesde($query, $fecha)
    {
        return $query->where('fecha', '>=', $fecha);
    }

    /**
     * Scope para una fecha específica
     */
    public function scopeParaFecha($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    /**
     * Accesorios útiles
     */

    /**
     * Retorna los espacios realmente disponibles (calculado en BD)
     */
    public function getEspaciosDisponiblesAttribute(): int
    {
        return $this->attributes['espacios_disponibles'] ??
            ($this->capacidad_dia - $this->espacios_usados - $this->espacios_bloqueados);
    }

    /**
     * Verifica si hay espacios disponibles
     */
    public function tieneEspaciosDisponibles(): bool
    {
        return $this->disponible === true && $this->espacios_disponibles > 0;
    }

    /**
     * Bloquea espacios manualmente (útil para administradores)
     */
    public function bloquearEspacios(int $cantidad): bool
    {
        if ($cantidad <= 0) return false;

        $this->espacios_bloqueados += $cantidad;
        return $this->save();
    }

    /**
     * Libera espacios bloqueados
     */
    public function liberarEspacios(int $cantidad): bool
    {
        if ($cantidad <= 0) return false;

        $this->espacios_bloqueados = max(0, $this->espacios_bloqueados - $cantidad);
        return $this->save();
    }
}
