<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasajero extends Model
{
    protected $table = 'pasajeros';

    protected $fillable = [
        'cliente_id',
        'reserva_id',
        'tour_id',
        'nombre',
        'apellido',
        'nombre_completo',
        'tipo_documento',
        'numero_documento',
        'pais_id',
        'idioma_id',
        'dieta_id',
        'email',
        'telefono',
        'whatsapp',
        'fecha_nacimiento',
        'genero',
        'edad',
        'notas_medicas',
        'pasaporte_expiracion',
        'pasaporte_imagen',
        'tam_peru',
        'contacto_emergencia',
        'telefono_emergencia',
        'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'pasaporte_expiracion' => 'date',
        'activo' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function idioma()
    {
        return $this->belongsTo(Idioma::class);
    }

    public function dieta()
    {
        return $this->belongsTo(Dieta::class);
    }
}
