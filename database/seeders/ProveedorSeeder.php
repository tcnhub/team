<?php

namespace Database\Seeders;

use App\Models\Proveedor;
use App\Models\TipoProveedor;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProveedorSeeder extends Seeder
{
    protected $faker;

    public function run(): void
    {
        $this->faker = Faker::create('es_PE');   // ← Se guarda como propiedad de la clase

        $tipos = TipoProveedor::where('activo', true)->get();

        if ($tipos->isEmpty()) {
            $this->command->error('No se encontraron tipos de proveedores. Ejecuta primero el TipoProveedorSeeder.');
            return;
        }

        $this->command->info('🌟 Creando 100 proveedores con Faker...');

        $ciudades = ['Cusco', 'Urubamba', 'Ollantaytambo', 'Machu Picchu Pueblo', 'Puno', 'Lima'];
        $distritos = ['Cusco', 'Wanchaq', 'Santiago', 'San Sebastián', 'San Jerónimo', 'Urubamba', 'Ollantaytambo'];
        $bancos = ['BCP', 'Interbank', 'BBVA', 'Scotiabank', 'Banco de la Nación'];

        for ($i = 0; $i < 100; $i++) {
            $tipo = $tipos->random();

            $tipoDocumento = $this->faker->randomElement(['RUC', 'DNI']);
            $numeroDocumento = $tipoDocumento === 'RUC'
                ? $this->faker->unique()->numerify('20#########')
                : $this->faker->unique()->numerify('########');

            $nombreComercial = $this->generarNombreComercial($tipo->slug);

            $fechaCreacion = $this->faker->dateTimeBetween('-2 years', 'now');

            Proveedor::create([
                'codigo_proveedor'      => 'PROV-' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'razon_social'          => $this->faker->company . ' S.A.C.',
                'nombre_comercial'      => $nombreComercial,

                'tipo_documento'        => $tipoDocumento,
                'numero_documento'      => $numeroDocumento,

                'tipo_proveedor_id'     => $tipo->id,

                'direccion'             => $this->faker->streetAddress,
                'distrito'              => $this->faker->randomElement($distritos),
                'ciudad'                => $this->faker->randomElement($ciudades),

                'telefono_principal'    => $this->faker->numerify('984#######'),
                'telefono_secundario'   => $this->faker->optional(0.65)->numerify('984#######'),

                'email_principal'       => $this->faker->unique()->safeEmail,
                'email_contabilidad'    => $this->faker->optional(0.55)->safeEmail,
                'pagina_web'            => $this->faker->optional(0.45)->url,

                'contacto_nombre'       => $this->faker->name,
                'contacto_cargo'        => $this->faker->randomElement(['Gerente General', 'Jefe de Reservas', 'Coordinador de Operaciones', 'Administrador']),
                'contacto_celular'      => $this->faker->numerify('984#######'),

                'estado'                => $this->faker->randomElement(['Activo', 'Activo', 'Activo', 'Inactivo']),
                'notas'                 => $this->faker->optional(0.40)->sentence(10),
                'calificacion'          => $this->faker->randomFloat(1, 3.8, 5.0),

                'ruc_vigente'           => $this->faker->boolean(92),
                'mincetour_calificado'  => $this->faker->boolean(68),

                'cuenta_bancaria'       => $this->faker->numerify('##############'),
                'banco'                 => $this->faker->randomElement($bancos),

                'moneda_principal'      => $this->faker->randomElement(['PEN', 'USD']),
                'condiciones_pago'      => $this->faker->randomElement(['30 días', '15 días', 'Pago inmediato', 'Transferencia 7 días']),
                'descuento_negociado'   => $this->faker->randomFloat(2, 0, 20),

                'logo_url'              => null,
                'capacidad_maxima'      => in_array($tipo->slug, ['hotel', 'restaurante'])
                    ? $this->faker->numberBetween(20, 350) : null,

                'horario_atencion'      => $this->faker->randomElement(['08:00-20:00', '07:00-22:00', '24 horas', '06:00-18:00']),
                'coordenadas_gps'       => '-13.' . $this->faker->numberBetween(500000, 580000) . ',-71.' . $this->faker->numberBetween(900000, 980000),
                'idiomas_atendidos'     => $this->faker->randomElement(['Español, Inglés', 'Español, Inglés, Francés', 'Español, Quechua', 'Español']),
                'certificaciones'       => $this->faker->optional(0.60)->randomElement(['Safe Travels', 'ISO 9001', 'Ministerio de Cultura', '']),

                // Fechas
                'fecha_registro'        => $fechaCreacion,
                'ultima_actualizacion'  => $fechaCreacion,
                'created_at'            => $fechaCreacion,
                'updated_at'            => $fechaCreacion,
            ]);
        }

        $this->command->info('✅ ¡100 proveedores creados correctamente!');
    }

    /**
     * Genera nombre comercial según el tipo de proveedor
     */
    private function generarNombreComercial(string $slug): string
    {
        return match ($slug) {
            'hotel'       => 'Hotel ' . $this->faker->randomElement(['Inka', 'Imperial', 'Palacio', 'Andino', 'Real', 'Tambo']) . ' ' . $this->faker->lastName,
            'restaurante' => 'Restaurante ' . $this->faker->randomElement(['Pachapapa', 'Inka Grill', 'Tupana', 'Qespi']) . ' ' . $this->faker->lastName,
            'guia'        => 'Guía ' . $this->faker->firstNameMale . ' ' . $this->faker->lastName,
            'transporte'  => 'Transportes ' . strtoupper($this->faker->randomLetter) . $this->faker->numerify('###') . ' Tours',
            'tren'        => $this->faker->randomElement(['PeruRail', 'Inca Rail']) . ' - ' . $this->faker->company,
            'entradas'    => 'Entradas Machu Picchu ' . $this->faker->company,
            'operador'    => $this->faker->company . ' Travel & Tours',
            default       => $this->faker->company . ' Servicios Turísticos'
        };
    }

    /**
     * Genera subcategoría según el tipo
     */
    private function generarCategoria(string $slug): ?string
    {
        return match ($slug) {
            'hotel'       => $this->faker->randomElement(['Hotel 3*', 'Hotel 4*', 'Hotel 5*', 'Hostal Boutique', 'Lodge']),
            'restaurante' => $this->faker->randomElement(['Restaurante Turístico', 'Gourmet', 'Andino', 'Internacional']),
            'guia'        => $this->faker->randomElement(['Guía Oficial', 'Guía Local', 'Guía Especializado en Arqueología']),
            'transporte'  => $this->faker->randomElement(['Bus Privado', 'Van Ejecutiva', 'Auto Sedán']),
            'tren'        => 'Tren Turístico',
            'entradas'    => 'Entradas Machu Picchu',
            'operador'    => 'Operador Receptivo',
            default       => null
        };
    }
}
