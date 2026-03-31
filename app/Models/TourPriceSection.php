<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPriceSection extends Model
{
    protected $fillable = [
        'tour_id',
        'tipo',
        'titulo',
        'descripcion',
        'anio',
        'orden',
    ];

    protected $casts = [
        'anio' => 'integer',
        'orden' => 'integer',
    ];

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function simpleItems()
    {
        return $this->hasMany(TourPriceSimpleItem::class, 'section_id')->orderBy('orden')->orderBy('id');
    }

    public function personRows()
    {
        return $this->hasMany(TourPricePersonRow::class, 'section_id')->orderBy('orden')->orderBy('id');
    }

    public function groupRows()
    {
        return $this->hasMany(TourPriceGroupRow::class, 'section_id')->orderBy('orden')->orderBy('id');
    }
}
