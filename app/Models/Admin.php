<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'admins';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'celular',
        'estado',
        'foto_perfil',
        'notas',
        'ultimo_acceso',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'ultimo_acceso'     => 'datetime',
        'estado'            => 'string',
    ];

    /**
     * ========================================
     * RELACIONES
     * ========================================
     */

    /**
     * Grupos (Biblia) creados por este admin
     */
    public function gruposCreados()
    {
        return $this->hasMany(Grupo::class, 'creado_por');
    }

    /**
     * Pagos registrados por este admin
     */
    public function pagosRegistrados()
    {
        return $this->hasMany(Pago::class, 'registrado_por');
    }

    /**
     * ========================================
     * SCOPES
     * ========================================
     */

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * ========================================
     * ACCESORES
     * ========================================
     */

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function getEstaActivoAttribute()
    {
        return $this->estado === 'activo';
    }

    /**
     * ========================================
     * MUTATORS
     * ========================================
     */

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}
