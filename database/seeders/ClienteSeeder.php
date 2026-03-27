<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Models\Pais;
use App\Models\Idioma;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $paises  = Pais::pluck('id', 'nombre');
        $idiomas = Idioma::pluck('id', 'nombre');

        $peruId      = $paises['Perú'] ?? $paises['Peru'] ?? null;
        $usaId       = $paises['Estados Unidos'] ?? $paises['United States'] ?? null;
        $ukId        = $paises['Reino Unido'] ?? $paises['United Kingdom'] ?? null;
        $alemaniaId  = $paises['Alemania'] ?? $paises['Germany'] ?? null;
        $franciaId   = $paises['Francia'] ?? $paises['France'] ?? null;
        $brasilId    = $paises['Brasil'] ?? $paises['Brazil'] ?? null;
        $australiaId = $paises['Australia'] ?? null;
        $canadaId    = $paises['Canadá'] ?? $paises['Canada'] ?? null;
        $argentinaId = $paises['Argentina'] ?? null;
        $espanaId    = $paises['España'] ?? $paises['Spain'] ?? null;

        $espId  = $idiomas['Español'] ?? $idiomas['Spanish'] ?? null;
        $ingId  = $idiomas['Inglés'] ?? $idiomas['English'] ?? null;
        $aleId  = $idiomas['Alemán'] ?? $idiomas['German'] ?? null;
        $fraId  = $idiomas['Francés'] ?? $idiomas['French'] ?? null;
        $porId  = $idiomas['Portugués'] ?? $idiomas['Portuguese'] ?? null;

        $nombresMasc = [
            'Carlos', 'José', 'Juan', 'Luis', 'Miguel', 'Jorge', 'Pedro', 'Marco',
            'Daniel', 'Eduardo', 'Roberto', 'Ricardo', 'Alejandro', 'Sergio', 'Diego',
            'Óscar', 'Raúl', 'Andrés', 'Felipe', 'Ernesto', 'Víctor', 'Rodrigo',
            'Héctor', 'Fernando', 'Álvaro', 'Gabriel', 'Pablo', 'César', 'Rubén', 'Iván',
        ];

        $nombresFem = [
            'María', 'Ana', 'Carmen', 'Rosa', 'Lucía', 'Patricia', 'Gloria', 'Sandra',
            'Claudia', 'Sofía', 'Paola', 'Valeria', 'Mariana', 'Jessica', 'Karina',
            'Mónica', 'Lorena', 'Gabriela', 'Andrea', 'Daniela', 'Isabel', 'Verónica',
            'Natalia', 'Silvia', 'Roxana', 'Fiorella', 'Milagros', 'Xiomara', 'Lesly', 'Wendy',
        ];

        $apellidos = [
            'Quispe', 'Mamani', 'Flores', 'García', 'Huanca', 'Condori', 'Cárdenas',
            'Torres', 'Vargas', 'Soto', 'Puma', 'López', 'Chávez', 'Rivera', 'Romero',
            'Mendoza', 'Guerrero', 'Castro', 'Ortiz', 'Morales', 'Paredes', 'Apaza',
            'Ccopa', 'Ttito', 'Huillca', 'Luque', 'Bellido', 'Ramos', 'Tapia', 'Salazar',
            'Benítez', 'Medina', 'Herrera', 'Cruz', 'Chalco', 'Molina', 'Fuentes', 'Nieto',
            'Arroyo', 'Espinoza', 'Villanueva', 'Coyla', 'Hancco', 'Layme', 'Cutipa',
            'Quispiccusi', 'Zuñiga', 'Pacori', 'Turpo', 'Callo',
        ];

        $nombresExt = [
            'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Thomas',
            'Emma', 'Olivia', 'Sophia', 'Isabella', 'Mia', 'Charlotte', 'Amelia', 'Harper',
            'Hans', 'Klaus', 'Friedrich', 'Greta', 'Heidi', 'Ingrid', 'Werner', 'Brigitte',
            'Pierre', 'Jean', 'Marie', 'Claire', 'Antoine', 'Camille', 'François', 'Isabelle',
            'Liam', 'Noah', 'Oliver', 'Ethan', 'Aiden', 'Sophie', 'Emily', 'Grace',
            'Lucas', 'Mateo', 'Valentina', 'Fernanda', 'Felipe', 'Thiago', 'Ana', 'Beatriz',
        ];

        $apellidosExt = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Miller', 'Davis', 'Wilson',
            'Moore', 'Taylor', 'Anderson', 'Thomas', 'Jackson', 'White', 'Harris', 'Clark',
            'Müller', 'Schmidt', 'Fischer', 'Weber', 'Wagner', 'Becker', 'Schulz', 'Hoffmann',
            'Martin', 'Bernard', 'Dubois', 'Robert', 'Richard', 'Petit', 'Moreau', 'Simon',
            'Thompson', 'Lewis', 'Walker', 'Hall', 'Allen', 'Young', 'King', 'Wright',
            'Silva', 'Santos', 'Oliveira', 'Souza', 'Costa', 'Ferreira', 'Alves', 'Pereira',
        ];

        $clientes = [];
        $docsUsados = [];

        // ── 110 clientes peruanos ──────────────────────────────────────────
        for ($i = 0; $i < 110; $i++) {
            $esMasc  = $i % 2 === 0;
            $nombre  = $esMasc
                ? $nombresMasc[$i % count($nombresMasc)]
                : $nombresFem[$i % count($nombresFem)];
            $apel1   = $apellidos[$i % count($apellidos)];
            $apel2   = $apellidos[($i + 7) % count($apellidos)];
            $apellido= "{$apel1} {$apel2}";

            do {
                $dni = str_pad((string) rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            } while (in_array($dni, $docsUsados));
            $docsUsados[] = $dni;

            $anioNac = rand(1960, 2003);
            $mesNac  = rand(1, 12);
            $diaNac  = rand(1, 28);

            $clientes[] = [
                'nombre'           => $nombre,
                'apellido'         => $apellido,
                'nombre_completo'  => "{$nombre} {$apellido}",
                'tipo_documento'   => 'dni',
                'numero_documento' => $dni,
                'pais_id'          => $peruId,
                'idioma_id'        => $espId,
                'genero'           => $esMasc ? 'male' : 'female',
                'fecha_nacimiento' => sprintf('%04d-%02d-%02d', $anioNac, $mesNac, $diaNac),
                'email'            => strtolower(
                    str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $nombre)
                    . '.' .
                    str_replace(['á','é','í','ó','ú','ü','ñ'], ['a','e','i','o','u','u','n'], $apel1)
                    . $i . '@ejemplo.pe'
                ),
                'telefono'         => '+51 9' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'activo'           => true,
            ];
        }

        // ── 80 clientes internacionales ───────────────────────────────────
        $intlConfig = [
            ['pais_id' => $usaId,       'idioma_id' => $ingId, 'prefijo' => 'US'],
            ['pais_id' => $ukId,        'idioma_id' => $ingId, 'prefijo' => 'GB'],
            ['pais_id' => $alemaniaId,  'idioma_id' => $aleId, 'prefijo' => 'DE'],
            ['pais_id' => $franciaId,   'idioma_id' => $fraId, 'prefijo' => 'FR'],
            ['pais_id' => $brasilId,    'idioma_id' => $porId, 'prefijo' => 'BR'],
            ['pais_id' => $australiaId, 'idioma_id' => $ingId, 'prefijo' => 'AU'],
            ['pais_id' => $canadaId,    'idioma_id' => $ingId, 'prefijo' => 'CA'],
            ['pais_id' => $argentinaId, 'idioma_id' => $espId, 'prefijo' => 'AR'],
            ['pais_id' => $espanaId,    'idioma_id' => $espId, 'prefijo' => 'ES'],
        ];

        for ($i = 0; $i < 80; $i++) {
            $esMasc  = $i % 2 === 0;
            $config  = $intlConfig[$i % count($intlConfig)];
            $nombre  = $nombresExt[$i % count($nombresExt)];
            $apel    = $apellidosExt[$i % count($apellidosExt)];
            $apellido= $apel;

            do {
                $passport = $config['prefijo'] . strtoupper(substr(md5(uniqid((string)$i, true)), 0, 8));
            } while (in_array($passport, $docsUsados));
            $docsUsados[] = $passport;

            $anioNac = rand(1960, 2003);
            $mesNac  = rand(1, 12);
            $diaNac  = rand(1, 28);

            $clientes[] = [
                'nombre'           => $nombre,
                'apellido'         => $apellido,
                'nombre_completo'  => "{$nombre} {$apellido}",
                'tipo_documento'   => 'passport',
                'numero_documento' => $passport,
                'pais_id'          => $config['pais_id'],
                'idioma_id'        => $config['idioma_id'],
                'genero'           => $esMasc ? 'male' : 'female',
                'fecha_nacimiento' => sprintf('%04d-%02d-%02d', $anioNac, $mesNac, $diaNac),
                'email'            => strtolower("{$nombre}.{$apel}{$i}@mail.com"),
                'telefono'         => null,
                'activo'           => true,
            ];
        }

        foreach ($clientes as $data) {
            Cliente::create($data);
        }

        $this->command->info('✓ ' . count($clientes) . ' clientes de prueba creados.');
    }
}
