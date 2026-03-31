<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPriceSimpleItem extends Model
{
    protected $fillable = [
        'section_id',
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
