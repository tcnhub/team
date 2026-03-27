<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pais;

class PaisSeeder extends Seeder
{
    public function run(): void
    {
        $paises = [
            ['nombre' => 'Peru', 'codigo_iso' => 'PER'],
            ['nombre' => 'Brazil', 'codigo_iso' => 'BRA'],
            ['nombre' => 'Netherlands', 'codigo_iso' => 'NLD'],
            ['nombre' => 'Canada', 'codigo_iso' => 'CAN'],
            ['nombre' => 'England', 'codigo_iso' => 'GBR'],
            ['nombre' => 'Belgium', 'codigo_iso' => 'BEL'],
            ['nombre' => 'Spain', 'codigo_iso' => 'ESP'],
            ['nombre' => 'United States', 'codigo_iso' => 'USA'],
        ];

        foreach ($paises as $pais) {
            Pais::firstOrCreate(['nombre' => $pais['nombre']], $pais);
        }
    }
}
