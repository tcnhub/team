<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->foreignId('tour_id')
                ->nullable()
                ->after('id_agente')
                ->constrained('tours')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['tour_id']);
            $table->dropColumn('tour_id');
        });
    }
};
