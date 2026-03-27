<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar moneda a USD por defecto y actualizar registros existentes
        DB::statement("ALTER TABLE reservas MODIFY COLUMN moneda CHAR(3) NOT NULL DEFAULT 'USD'");
        DB::statement("ALTER TABLE pagos MODIFY COLUMN moneda ENUM('USD') NOT NULL DEFAULT 'USD'");
        DB::statement("ALTER TABLE tours MODIFY COLUMN moneda CHAR(3) NOT NULL DEFAULT 'USD'");

        // Actualizar registros existentes
        DB::table('reservas')->update(['moneda' => 'USD']);
        DB::table('pagos')->update(['moneda' => 'USD']);
        DB::table('tours')->update(['moneda' => 'USD']);
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE reservas MODIFY COLUMN moneda CHAR(3) NOT NULL DEFAULT 'PEN'");
        DB::statement("ALTER TABLE pagos MODIFY COLUMN moneda ENUM('PEN','USD') NOT NULL DEFAULT 'PEN'");
        DB::statement("ALTER TABLE tours MODIFY COLUMN moneda CHAR(3) NOT NULL DEFAULT 'PEN'");
    }
};
