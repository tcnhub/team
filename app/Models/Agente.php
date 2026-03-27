<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agentes';

    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo_agente',
        'nombres',
        'apellidos',
        'email',
        'telefono',
        'celular',
        'dni',
        'fecha_nacimiento',
        'direccion',
        'ciudad',
        'pais',
        'genero',
        'estado',
        'fecha_ingreso',
        'fecha_salida',
        'comision_porcentaje',
        'departamento',
        'notas',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso'    => 'date',
        'fecha_salida'     => 'date',
        'comision_porcentaje' => 'decimal:2',
    ];

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Relaciones
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_agente', 'id');
    }

    public function usuario()
    {
        return $this->hasOne(User::class); // Si cada agente tiene un usuario del sistema
    }
}
