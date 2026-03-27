<?php



namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Hotel',            'slug' => 'hotel',           'icono' => '🏨', 'orden' => 1],
            ['nombre' => 'Restaurante',      'slug' => 'restaurante',     'icono' => '🍽️', 'orden' => 2],
            ['nombre' => 'Guía de Turismo',  'slug' => 'guia',            'icono' => '👨‍🏫', 'orden' => 3],
            ['nombre' => 'Transporte',       'slug' => 'transporte',      'icono' => '🚌', 'orden' => 4],
            ['nombre' => 'Tren',             'slug' => 'tren',            'icono' => '🚂', 'orden' => 5],
            ['nombre' => 'Entradas',         'slug' => 'entradas',        'icono' => '🎟️', 'orden' => 6],
            ['nombre' => 'Operador Turístico','slug' => 'operador',       'icono' => '🌎', 'orden' => 7],
            ['nombre' => 'Agencia Subcontratada', 'slug' => 'agencia', 'icono' => '🏢', 'orden' => 8],
            ['nombre' => 'Otros',            'slug' => 'otros',           'icono' => '📌', 'orden' => 99],
        ];

        DB::table('tipo_proveedores')->insert($tipos);
    }
}
