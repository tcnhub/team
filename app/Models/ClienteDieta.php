<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClienteDieta extends Model
{
    use HasFactory;

    protected $table = 'cliente_dieta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cliente_id',
        'dieta_id',
    ];

    /**
     * Las columnas que no deben ser asignadas en masa
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Relaciones
     */

    /**
     * Pertenece a un Cliente
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id')
            ->withTrashed(); // Si quieres ver aunque el cliente esté eliminado
    }

    /**
     * Pertenece a una Dieta
     */
    public function dieta(): BelongsTo
    {
        return $this->belongsTo(Dieta::class, 'dieta_id');
    }

    /**
     * Scopes útiles
     */

    /**
     * Scope para filtrar por cliente específico
     */
    public function scopePorCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    /**
     * Scope para filtrar por dieta específica
     */
    public function scopePorDieta($query, $dietaId)
    {
        return $query->where('dieta_id', $dietaId);
    }
}
