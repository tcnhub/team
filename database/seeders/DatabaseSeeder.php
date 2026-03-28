<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PaisSeeder::class,
            IdiomaSeeder::class,
            DietaSeeder::class,
            TipoProveedorSeeder::class,
            ProveedorSeeder::class,
            AdminSeeder::class,
            TourSeeder::class,
            AddonSeeder::class,
            AgenteSeeder::class,
            ClienteSeeder::class,
            PasajeroSeeder::class,
        ]);
    }
}
