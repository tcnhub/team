<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPriceGroupRow extends Model
{
    protected $fillable = [
        'section_id',
        'etiqueta_grupo',
        'descripcion',
        'precio_por_grupo',
        'orden',
    ];

    protected $casts = [
        'precio_por_grupo' => 'decimal:2',
        'orden' => 'integer',
    ];

    public function section()
    {
        return $this->belongsTo(TourPriceSection::class, 'section_id');
    }
}
