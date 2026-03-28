<?php

namespace Database\Seeders;

use App\Models\Configuracion;
use Illuminate\Database\Seeder;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nombre' => 'comision de Izipay', 'valor' => '5'],
            ['nombre' => 'Comision de paypal', 'valor' => '6'],
            ['nombre' => 'Nombre de la Empresa', 'valor' => 'Machu Picchu team travel SAC'],
            ['nombre' => 'RUC', 'valor' => '99999999'],
            ['nombre' => 'Direccion', 'valor' => 'CValle Cusco 123'],
            ['nombre' => 'telefono', 'valor' => '9845858585'],
            ['nombre' => 'Pagina Web', 'valor' => 'https://www.machupicchuteam.com'],
        ];

        foreach ($items as $item) {
            Configuracion::updateOrCreate(['nombre' => $item['nombre']], $item);
        }
    }
}
