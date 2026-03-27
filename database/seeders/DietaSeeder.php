<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dieta;

class DietaSeeder extends Seeder
{
    public function run(): void
    {
        $dietas = [
            ['nombre' => 'None', 'descripcion' => 'Sin restriccion alimentaria'],
            ['nombre' => 'Vegetarian', 'descripcion' => 'No consume carne'],
            ['nombre' => 'Vegan', 'descripcion' => 'No consume productos de origen animal'],
            ['nombre' => 'Gluten-Free', 'descripcion' => 'Sin gluten'],
            ['nombre' => 'Lactose-Free', 'descripcion' => 'Sin lactosa'],
        ];

        foreach ($dietas as $dieta) {
            Dieta::firstOrCreate(['nombre' => $dieta['nombre']], $dieta);
        }
    }
}
