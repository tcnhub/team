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
        'estado'           => 'boolean',
        'comision_porcentaje' => 'decimal:2',
    ];

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    public function setEstadoAttribute($value): void
    {
        $this->attributes['estado'] = in_array($value, [true, 1, '1', 'activo'], true) ? 1 : 0;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
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
