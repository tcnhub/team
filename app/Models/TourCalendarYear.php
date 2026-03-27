<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourCalendarYear extends Model
{
    protected $table = 'tour_calendar_years';

    public $timestamps = false;

    protected $fillable = [
        'tour_id',
        'anio',
        'capacidad_anio',
    ];

    protected $casts = [
        'anio'          => 'integer',
        'es_bisiesto'   => 'boolean',
        'total_dias'    => 'integer',
        'capacidad_anio'=> 'integer',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    public function dias(): HasMany
    {
        return $this->hasMany(TourAvailability::class, 'calendar_year_id');
    }
}
