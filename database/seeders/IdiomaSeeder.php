<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Idioma;

class IdiomaSeeder extends Seeder
{
    public function run(): void
    {
        $idiomas = [
            ['nombre' => 'Spanish', 'codigo' => 'es'],
            ['nombre' => 'English', 'codigo' => 'en'],
            ['nombre' => 'Portuguese', 'codigo' => 'pt'],
            ['nombre' => 'French', 'codigo' => 'fr'],
            ['nombre' => 'Dutch', 'codigo' => 'nl'],
            ['nombre' => 'German', 'codigo' => 'de'],
        ];

        foreach ($idiomas as $idioma) {
            Idioma::firstOrCreate(['nombre' => $idioma['nombre']], $idioma);
        }
    }
}
