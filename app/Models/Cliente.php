<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';

    protected $fillable = [
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

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function idioma()
    {
        return $this->belongsTo(Idioma::class);
    }

    /**
     * Dieta principal asignada directamente (FK dieta_id en clientes)
     */
    public function dieta()
    {
        return $this->belongsTo(Dieta::class, 'dieta_id');
    }

    /**
     * Dietas adicionales vía tabla pivote cliente_dieta
     */
    public function dietas()
    {
        return $this->belongsToMany(Dieta::class, 'cliente_dieta');
    }


    // nuevas relacionoes agregadas

    public function reservas() { return $this->hasMany(Reserva::class, 'id_cliente'); }

}
