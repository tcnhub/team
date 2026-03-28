<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['nombre' => 'Carpa adicional', 'descripcion' => 'Servicio de carpa adicional para la reserva.', 'monto' => 500],
            ['nombre' => 'Baston de Trekking', 'descripcion' => 'Baston adicional para trekking.', 'monto' => 30],
            ['nombre' => 'Un caballo adicional', 'descripcion' => 'Caballo extra para apoyo en ruta.', 'monto' => 40],
            ['nombre' => 'Asccenso a Huayna Picchu', 'descripcion' => 'Ingreso adicional a Huayna Picchu.', 'monto' => 200],
            ['nombre' => 'Asccenso a machu Picchu', 'descripcion' => 'Ingreso adicional a Machu Picchu.', 'monto' => 200],
            ['nombre' => 'Sacred Valley Tour', 'descripcion' => 'Tour adicional al Valle Sagrado.', 'monto' => 200],
            ['nombre' => 'Comida Gurmet', 'descripcion' => 'Servicio adicional de comida gourmet.', 'monto' => 400],
        ];

        foreach ($addons as $addon) {
            Addon::updateOrCreate(['nombre' => $addon['nombre']], $addon);
        }
    }
}
