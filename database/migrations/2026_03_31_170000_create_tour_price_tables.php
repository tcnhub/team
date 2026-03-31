<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_price_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')
                ->constrained('tours')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('tipo', ['simple', 'por_persona', 'por_grupo']);
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->year('anio')->nullable()
                ->comment('NULL = aplica a todos los años; valor = solo ese año');
            $table->smallInteger('orden')->unsigned()->default(0);
            $table->timestamps();

            $table->index(['tour_id', 'anio']);
        });

        Schema::create('tour_price_simple_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('tour_price_sections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_por_persona', 10, 2);
            $table->smallInteger('orden')->unsigned()->default(0);
            $table->timestamps();

            $table->index('section_id');
        });

        Schema::create('tour_price_person_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('tour_price_sections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('etiqueta_personas', 100);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_por_persona', 10, 2);
            $table->smallInteger('orden')->unsigned()->default(0);
            $table->timestamps();

            $table->index('section_id');
        });

        Schema::create('tour_price_group_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')
                ->constrained('tour_price_sections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('etiqueta_grupo', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_por_grupo', 10, 2);
            $table->smallInteger('orden')->unsigned()->default(0);
            $table->timestamps();

            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_price_group_rows');
        Schema::dropIfExists('tour_price_person_rows');
        Schema::dropIfExists('tour_price_simple_items');
        Schema::dropIfExists('tour_price_sections');
    }
};
