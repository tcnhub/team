<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

class TourPricingSeeder extends Seeder
{
    public function run(): void
    {
        $tour = Tour::query()->orderBy('id')->first();

        if (! $tour) {
            return;
        }

        if ($tour->priceSections()->exists()) {
            return;
        }

        $simple = $tour->priceSections()->create([
            'tipo' => 'simple',
            'titulo' => 'Tarifa regular',
            'descripcion' => 'Precio unitario base aplicable durante todo el año.',
            'anio' => null,
            'orden' => 1,
        ]);

        $simple->simpleItems()->createMany([
            [
                'descripcion' => 'Servicio compartido con salida programada.',
                'precio_por_persona' => 450.00,
                'orden' => 1,
            ],
            [
                'descripcion' => 'Servicio premium con asistencia reforzada.',
                'precio_por_persona' => 520.00,
                'orden' => 2,
            ],
        ]);

        $perPerson = $tour->priceSections()->create([
            'tipo' => 'por_persona',
            'titulo' => 'Tarifa privada por número de personas',
            'descripcion' => 'Mientras menos viajeros, mayor tarifa por persona.',
            'anio' => (int) now()->format('Y'),
            'orden' => 2,
        ]);

        $perPerson->personRows()->createMany([
            [
                'etiqueta_personas' => '1 persona',
                'descripcion' => 'Servicio privado exclusivo.',
                'precio_por_persona' => 690.00,
                'orden' => 1,
            ],
            [
                'etiqueta_personas' => '2 personas',
                'descripcion' => 'Tarifa privada para pareja o dos viajeros.',
                'precio_por_persona' => 560.00,
                'orden' => 2,
            ],
            [
                'etiqueta_personas' => '3 a 4 personas',
                'descripcion' => 'Ideal para familias o grupos pequeños.',
                'precio_por_persona' => 510.00,
                'orden' => 3,
            ],
        ]);

        $perGroup = $tour->priceSections()->create([
            'tipo' => 'por_grupo',
            'titulo' => 'Tarifa charter por grupo',
            'descripcion' => 'Costo cerrado por rango de integrantes.',
            'anio' => (int) now()->format('Y'),
            'orden' => 3,
        ]);

        $perGroup->groupRows()->createMany([
            [
                'etiqueta_grupo' => 'Grupos de 2 a 5 personas',
                'descripcion' => 'Servicio privado con transporte exclusivo.',
                'precio_por_grupo' => 2100.00,
                'orden' => 1,
            ],
            [
                'etiqueta_grupo' => 'Grupos de 6 a 10 personas',
                'descripcion' => 'Incluye coordinación operativa dedicada.',
                'precio_por_grupo' => 3650.00,
                'orden' => 2,
            ],
        ]);
    }
}
