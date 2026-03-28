<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->string('tipo_pasajero', 20)->default('adulto')->after('tour_id');
        });

        DB::table('pasajeros')->whereNull('tipo_pasajero')->update(['tipo_pasajero' => 'adulto']);
    }

    public function down(): void
    {
        Schema::table('pasajeros', function (Blueprint $table) {
            $table->dropColumn('tipo_pasajero');
        });
    }
};
