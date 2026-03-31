<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPricePersonRow extends Model
{
    protected $fillable = [
        'section_id',
        'etiqueta_personas',
        'descripcion',
        'precio_por_persona',
        'orden',
    ];

    protected $casts = [
        'precio_por_persona' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(TourPriceSection::class, 'section_id');
    }
}
