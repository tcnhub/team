<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dieta extends Model
{
    protected $table = 'dietas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function clientesPivot()
    {
        return $this->hasMany(ClienteDieta::class); // si usas la tabla pivote
    }
}
