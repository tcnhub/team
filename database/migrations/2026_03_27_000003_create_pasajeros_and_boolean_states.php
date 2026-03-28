<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pasajeros')) {
            Schema::create('pasajeros', function (Blueprint $table) {
                $table->id();
                $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
                $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
                $table->foreignId('tour_id')->constrained('tours')->cascadeOnDelete();
                $table->string('nombre');
                $table->string('apellido');
                $table->string('nombre_completo')->nullable();
                $table->enum('tipo_documento', ['passport', 'dni', 'id'])->default('passport');
                $table->string('numero_documento')->unique();
                $table->foreignId('pais_id')->nullable()->constrained('paises')->nullOnDelete();
                $table->foreignId('idioma_id')->nullable()->constrained('idiomas')->nullOnDelete();
                $table->foreignId('dieta_id')->nullable()->constrained('dietas')->nullOnDelete();
                $table->string('email')->nullable();
                $table->string('telefono')->nullable();
                $table->string('whatsapp')->nullable();
                $table->date('fecha_nacimiento')->nullable();
                $table->enum('genero', ['male', 'female', 'other'])->nullable();
                $table->integer('edad')->nullable();
                $table->text('notas_medicas')->nullable();
                $table->date('pasaporte_expiracion')->nullable();
                $table->string('pasaporte_imagen')->nullable();
                $table->string('tam_peru')->nullable();
                $table->string('contacto_emergencia')->nullable();
                $table->string('telefono_emergencia')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();

                $table->index(['cliente_id', 'reserva_id', 'tour_id']);
            });
        }

        if (Schema::hasColumn('categorias', 'activo') && !Schema::hasColumn('categorias', 'estado')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->boolean('estado')->default(true)->after('icono');
            });

            DB::statement('UPDATE categorias SET estado = activo');

            Schema::table('categorias', function (Blueprint $table) {
                $table->dropColumn('activo');
            });
        }

        if (Schema::hasColumn('agentes', 'estado')) {
            DB::statement("
                UPDATE agentes
                SET estado = CASE
                    WHEN estado IN ('activo', '1', 1, true) THEN 1
                    ELSE 0
                END
            ");

            DB::statement('ALTER TABLE agentes MODIFY estado TINYINT(1) NOT NULL DEFAULT 1');
        }

        if (Schema::hasColumn('tours', 'estado')) {
            DB::statement("
                UPDATE tours
                SET estado = CASE
                    WHEN estado IN ('Activo', '1', 1, true) THEN 1
                    ELSE 0
                END
            ");

            DB::statement('ALTER TABLE tours MODIFY estado TINYINT(1) NOT NULL DEFAULT 1');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pasajeros');

        if (!Schema::hasColumn('categorias', 'activo') && Schema::hasColumn('categorias', 'estado')) {
            Schema::table('categorias', function (Blueprint $table) {
                $table->boolean('activo')->default(true)->after('icono');
            });

            DB::statement('UPDATE categorias SET activo = estado');

            Schema::table('categorias', function (Blueprint $table) {
                $table->dropColumn('estado');
            });
        }

        if (Schema::hasColumn('agentes', 'estado')) {
            DB::statement("
                ALTER TABLE agentes
                MODIFY estado ENUM('activo', 'inactivo', 'vacaciones', 'baja') NOT NULL DEFAULT 'activo'
            ");
            DB::statement("UPDATE agentes SET estado = CASE WHEN estado = 1 THEN 'activo' ELSE 'inactivo' END");
        }

        if (Schema::hasColumn('tours', 'estado')) {
            DB::statement("
                ALTER TABLE tours
                MODIFY estado ENUM('Activo', 'Inactivo', 'Agotado', 'Cancelado') NOT NULL DEFAULT 'Activo'
            ");
            DB::statement("UPDATE tours SET estado = CASE WHEN estado = 1 THEN 'Activo' ELSE 'Inactivo' END");
        }
    }
};
