<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'nombre',
        'valor',
    ];

    public static function valor(string $nombre, ?string $default = null): ?string
    {
        return static::where('nombre', $nombre)->value('valor') ?? $default;
    }
}
