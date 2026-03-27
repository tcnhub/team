<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'nombre'        => 'Super',
            'apellido'      => 'Admin',
            'email'         => 'admin@team.com',
            'password'      => '12345678', // Tu mutator setPasswordAttribute la encriptará
            'telefono'      => '0123456',
            'celular'       => '+51987654321',
            'estado'        => 'activo',
            'foto_perfil'   => null,
            'notas'         => 'Administrador principal del sistema (Creado por Seeder).',
            'ultimo_acceso' => now(),
        ]);
    }
}
