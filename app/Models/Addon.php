<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;

    protected $table = 'addons';

    protected $fillable = [
        'nombre',
        'descripcion',
        'monto',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    public function tours()
    {
        return $this->belongsToMany(Tour::class, 'addon_tour')->withTimestamps();
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'addon_reserva')
            ->withPivot(['cantidad', 'monto_unitario', 'monto_total'])
            ->withTimestamps();
    }
}
