<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addons', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('monto', 10, 2);
            $table->timestamps();
        });

        Schema::create('addon_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('addons')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['tour_id', 'addon_id']);
        });

        Schema::create('addon_reserva', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('addon_id')->constrained('addons')->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->decimal('monto_unitario', 10, 2);
            $table->decimal('monto_total', 10, 2);
            $table->timestamps();
            $table->unique(['reserva_id', 'addon_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addon_reserva');
        Schema::dropIfExists('addon_tour');
        Schema::dropIfExists('addons');
    }
};
