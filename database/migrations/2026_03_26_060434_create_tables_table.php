<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            // Información básica
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('password');

            // Contacto
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();

            // Estado simple
            $table->enum('estado', ['activo', 'inactivo', 'bloqueado'])
                ->default('activo');

            // Información adicional
            $table->string('foto_perfil', 255)->nullable();
            $table->text('notas')->nullable();

            // Seguridad
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('ultimo_acceso')->nullable();

            $table->timestamps();        // created_at y updated_at
            $table->softDeletes();       // deleted_at
        });


        // 1. Tabla de Categorías (más claro que "tour_tipos")
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();           // Cultural, Aventura, Day Tour, Ecológico, Luxury...
            $table->string('descripcion')->nullable();
            $table->string('color')->nullable();
            $table->string('icono')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // 2. Tabla tours (sin cambios importantes)
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_tour', 20)->unique();
            $table->string('nombre_tour', 150);
            $table->text('descripcion_corta')->nullable();
            $table->longText('descripcion_larga')->nullable();
            $table->integer('duracion_dias')->nullable();
            $table->integer('duracion_noches')->nullable();
            $table->enum('nivel_dificultad', ['Fácil', 'Moderado', 'Difícil', 'Extremo'])->nullable();
            $table->decimal('precio_base', 10, 2)->nullable();
            $table->char('moneda', 3)->default('PEN');
            $table->integer('max_personas')->nullable();
            $table->integer('min_personas')->nullable();
            $table->string('salida_desde', 100)->nullable();
            $table->string('destino_principal', 100)->nullable();
            $table->text('incluye')->nullable();
            $table->text('no_incluye')->nullable();
            $table->json('itinerario')->nullable();
            $table->json('galeria_imagenes')->nullable();
            $table->boolean('estado')->default(true);
            $table->boolean('destacado')->default(false);
            $table->timestamps();
        });


        // Años calendarios por tour
        Schema::create('tour_calendar_years', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_id')
                ->constrained('tours')
                ->onDelete('cascade');

            $table->smallInteger('anio')->unsigned();

            // Columnas calculadas (equivalentes a las GENERATED ALWAYS AS de MySQL)
            $table->boolean('es_bisiesto')->storedAs(
                '(anio % 4 = 0 AND anio % 100 <> 0) OR (anio % 400 = 0)'
            );

            $table->smallInteger('total_dias')->storedAs(
                'IF((anio % 4 = 0 AND anio % 100 <> 0) OR (anio % 400 = 0), 366, 365)'
            );

            // Override de capacidad diaria para ese año; NULL = usa tours.capacidad_diaria
            $table->integer('capacidad_anio')->nullable()
                ->comment('Override del default del tour para este año; NULL = usa tours.capacidad_diaria');

            $table->timestamp('created_at')->useCurrent();

            $table->unique(['tour_id', 'anio'], 'uq_tour_anio');
        });

        // Disponibilidad diaria por tour
        Schema::create('tour_availability', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tour_id')
                ->constrained('tours')
                ->onDelete('cascade');

            $table->foreignId('calendar_year_id')
                ->constrained('tour_calendar_years')
                ->onDelete('cascade');

            $table->date('fecha');

            $table->integer('capacidad_dia')->default(300);
            $table->integer('espacios_usados')->default(0);

            $table->integer('espacios_bloqueados')->default(0)
                ->comment('Bloqueados manualmente por el admin; descuentan de disponibles');

            // Columnas calculadas stored
            $table->integer('espacios_disponibles')->storedAs(
                '(capacidad_dia - espacios_usados) - espacios_bloqueados'
            );

            $table->boolean('disponible')->storedAs(
                '(espacios_usados + espacios_bloqueados) < capacidad_dia'
            );

            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Restricciones e índices
            $table->unique(['tour_id', 'fecha'], 'uq_tour_fecha');
            $table->index('fecha', 'idx_availability_fecha');
            $table->index('disponible', 'idx_availability_disponible');
        });



        // 3. Tabla Pivote - ¡Mucho más lógica!
        Schema::create('categoria_tour', function (Blueprint $table) {
            $table->id();

            $table->foreignId('categoria_id')
                ->constrained('categorias')
                ->onDelete('cascade');

            $table->foreignId('tour_id')
                ->constrained('tours')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['categoria_id', 'tour_id']);
        });

        // 4. Tabla de precios (sin cambios)
        Schema::create('tour_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained()->onDelete('cascade');
            $table->string('etiqueta', 100);
            $table->decimal('precio', 10, 2);
            $table->char('moneda', 3)->default('PEN');
            $table->string('descripcion', 255)->nullable();
            $table->integer('min_personas')->nullable();
            $table->integer('max_personas')->nullable();
            $table->boolean('es_predeterminado')->default(false);
            $table->timestamps();

            $table->unique(['tour_id', 'etiqueta']);
        });


        Schema::create('paises', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('codigo_iso', 3)->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('idiomas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('codigo', 10)->nullable()->unique();
            $table->timestamps();
        });



        Schema::create('dietas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });



        // 5. tabla de clientes
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();

            // Datos personales
            $table->string('nombre');
            $table->string('apellido');
            $table->string('nombre_completo')->nullable();

            // Identificación
            $table->enum('tipo_documento', ['passport', 'dni', 'id'])->default('passport');
            $table->string('numero_documento')->unique();

            // Relaciones
            $table->foreignId('pais_id')->nullable()->constrained('paises')->nullOnDelete();
            $table->foreignId('idioma_id')->nullable()->constrained('idiomas')->nullOnDelete();
            $table->foreignId('dieta_id')->nullable()->constrained('dietas')->nullOnDelete();

            // Contacto
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();

            // Datos adicionales
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['male', 'female', 'other'])->nullable();
            $table->integer('edad')->nullable();

            // Viaje / operación
            $table->text('notas_medicas')->nullable();
            $table->date('pasaporte_expiracion')->nullable();
            $table->string('pasaporte_imagen')->nullable();
            $table->string('tam_peru')->nullable();

            // Emergencia
            $table->string('contacto_emergencia')->nullable();
            $table->string('telefono_emergencia')->nullable();

            // Estado
            $table->boolean('activo')->default(true);

            $table->timestamps();
        });

        Schema::create('cliente_dieta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('dieta_id')->constrained('dietas')->cascadeOnDelete();
            $table->timestamps();
        });


        // tabla de agentes

        Schema::create('agentes', function (Blueprint $table) {
            $table->id(); // id_agente

            $table->string('codigo_agente', 20)->unique()->nullable();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('nombre_completo')->virtualAs("CONCAT(nombres, ' ', apellidos)");

            $table->string('email')->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20);

            $table->string('dni', 20)->unique()->nullable();           // Documento de identidad (Perú)
            $table->date('fecha_nacimiento')->nullable();

            $table->string('direccion')->nullable();
            $table->string('ciudad', 100)->default('Lima');
            $table->string('pais', 100)->default('Perú');

            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();

            // Estado del agente
            $table->boolean('estado')->default(true);

            // Información laboral
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_salida')->nullable();

            $table->decimal('comision_porcentaje', 5, 2)->default(0.00); // % de comisión
            $table->string('departamento', 100)->nullable(); // Ventas, Corporativo, etc.

            $table->text('notas')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        // tabla de reservas





        Schema::create('reservas', function (Blueprint $table) {
            $table->id();                    // id_reserva (BIGINT AUTO_INCREMENT)

            $table->string('codigo_reserva', 20)->unique();
            $table->timestamp('fecha_reserva')->useCurrent();

            $table->enum('estado_reserva', [
                'pendiente',
                'confirmada',
                'pagada',
                'cancelada',
                'reembolsada',
                'completada'
            ])->default('pendiente');

            // Relaciones principales
            $table->foreignId('id_cliente')
                ->constrained('clientes')
                ->onDelete('cascade');

            $table->foreignId('id_agente')
                ->nullable()
                ->constrained('agentes')
                ->onDelete('set null');

            // Tipo y descripción del servicio
            $table->string('tipo_reserva', 50);           // Paquete, Vuelo, Hotel, Crucero, etc.
            $table->string('descripcion_servicio', 255)->nullable();

            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            $table->string('origen', 100)->nullable();
            $table->string('destino', 100)->nullable();

            // Pasajeros
            $table->integer('num_pasajeros')->default(1);
            $table->integer('num_adultos')->default(1);
            $table->integer('num_ninos')->default(0);
            $table->integer('num_bebes')->default(0);

            // Información financiera
            // esto va ser calculado de acuerdo la informacion de la tabla de pagos
            $table->char('moneda', 3)->default('PEN');
            $table->decimal('precio_total', 12, 2);
            $table->decimal('descuento', 10, 2)->default(0.00);
            $table->decimal('precio_final', 12, 2);
            $table->decimal('monto_pagado', 12, 2)->default(0.00);
            $table->decimal('saldo_pendiente', 12, 2)->virtualAs('precio_final - monto_pagado');

            // Información adicional
            $table->text('notas')->nullable();
            $table->text('requisitos_especiales')->nullable();
            $table->string('fuente_reserva', 50)->nullable();   // Web, WhatsApp, Oficina, etc.

            $table->timestamp('fecha_cancelacion')->nullable();
            $table->text('motivo_cancelacion')->nullable();

            // Dentro del Schema::create('reservas', ...) agrega esto:
            $table->foreignId('availability_id')
                ->nullable()
                ->constrained('tour_availability')
                ->onDelete('restrict');

            // Timestamps automáticos
            $table->timestamps();   // created_at y updated_at
            $table->softDeletes();  // deleted_at (recomendado para reservas)
        });

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







        Schema::create('tipo_proveedores', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 80)->unique();                    // Nombre del tipo (Hotel, Guía, etc.)
            $table->string('slug', 80)->unique();                      // Para usar en URLs o código interno (hotel, guia, etc.)
            $table->string('descripcion', 255)->nullable();

            // Icono o emoji para mostrar en la interfaz (opcional pero útil)
            $table->string('icono', 50)->nullable();                   // Ej: '🏨', '🚌', '👨‍🏫'

            // Orden de visualización en listas y formularios
            $table->integer('orden')->default(0);

            $table->boolean('activo')->default(true);

            $table->timestamps();

            // Índices
            $table->index('activo');
            $table->index('orden');
        });

        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();

            $table->string('codigo_proveedor', 20)->unique()->nullable();
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 150);

            $table->string('tipo_documento', 4);
            $table->string('numero_documento', 15)->unique();

            // ←←← CAMBIO IMPORTANTE AQUÍ
            $table->foreignId('tipo_proveedor_id')
                ->constrained('tipo_proveedores')
                ->onDelete('restrict');   // No permite eliminar un tipo si tiene proveedores



            $table->string('direccion', 255);
            $table->string('distrito', 100)->nullable();
            $table->string('ciudad', 100);

            $table->string('telefono_principal', 20);
            $table->string('telefono_secundario', 20)->nullable();

            $table->string('email_principal', 150);
            $table->string('email_contabilidad', 150)->nullable();
            $table->string('pagina_web', 255)->nullable();

            $table->string('contacto_nombre', 150)->nullable();
            $table->string('contacto_cargo', 100)->nullable();
            $table->string('contacto_celular', 20)->nullable();

            $table->enum('estado', ['Activo', 'Inactivo', 'Bloqueado'])->default('Activo');

            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('ultima_actualizacion')->useCurrentOnUpdate();

            $table->text('notas')->nullable();
            $table->decimal('calificacion', 2, 1)->nullable();

            $table->boolean('ruc_vigente')->default(true);
            $table->boolean('mincetour_calificado')->default(false);

            $table->string('cuenta_bancaria', 100)->nullable();
            $table->string('banco', 100)->nullable();

            $table->enum('moneda_principal', ['PEN', 'USD'])->default('PEN');
            $table->string('condiciones_pago', 255)->nullable();
            $table->decimal('descuento_negociado', 5, 2)->nullable()->default(0);

            $table->string('logo_url', 255)->nullable();
            $table->integer('capacidad_maxima')->nullable();
            $table->string('horario_atencion', 100)->nullable();
            $table->string('coordenadas_gps', 50)->nullable();
            $table->string('idiomas_atendidos', 100)->nullable();
            $table->text('certificaciones')->nullable();

            $table->timestamps();

            // Índices
            $table->index('estado');
            $table->index('ciudad');
            $table->index(['numero_documento', 'tipo_documento']);
        });



        Schema::create('grupos', function (Blueprint $table) {
            $table->id();

            // Código identificador de la Biblia/Grupo
            $table->string('codigo_grupo', 25)->unique();           // Ej: GRP-202603-045 o BIBLIA-2026-001

            $table->string('nombre_grupo', 150);                    // Nombre del tour o grupo (ej: "Machu Picchu 4D/3N - Marzo 2026")

            // Relación con la Reserva
            $table->foreignId('reserva_id')
                ->nullable()
                ->constrained('reservas')
                ->onDelete('set null');

            // Estado del grupo
            $table->enum('estado', [
                'pendiente',      // Aún no inicia
                'confirmado',     // Confirmado y listo
                'en_ejecucion',   // El tour está en curso
                'completado',     // Finalizado
                'cancelado'
            ])->default('pendiente');

            // Fechas del tour
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();

            // Información de pasajeros
            $table->integer('num_pasajeros')->default(1);
            $table->integer('num_adultos')->default(1);
            $table->integer('num_ninos')->default(0);
            $table->integer('num_bebes')->default(0);

            // Datos financieros (resumen)
            $table->char('moneda', 3)->default('PEN');
            $table->decimal('precio_total', 12, 2)->default(0.00);      // Precio total del paquete
            $table->decimal('monto_pagado', 12, 2)->default(0.00);
            $table->decimal('saldo_pendiente', 12, 2)->virtualAs('precio_total - monto_pagado');

            // Información del tour
            $table->string('destino_principal', 100);                   // Machu Picchu, Valle Sagrado, Puno, etc.
            $table->string('itinerario_tipo', 100)->nullable();         // Clasico, Premium, Privado, etc.

            // Guía y transporte principal sacando de la tabla de proveedores
            $table->foreignId('guia_principal_id')
                ->nullable()
                ->constrained('proveedores')
                ->onDelete('set null');

            $table->foreignId('chofer_principal_id')
                ->nullable()
                ->constrained('proveedores')
                ->onDelete('set null');

            // Datos adicionales
            $table->text('notas_operativas')->nullable();               // Notas importantes para operaciones
            $table->text('observaciones')->nullable();

            // Quién creó la Biblia
            $table->foreignId('creado_por')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->timestamp('fecha_creacion_biblia')->useCurrent();

            $table->timestamps();        // created_at y updated_at
            $table->softDeletes();       // deleted_at (recomendado)

            // Índices importantes
            $table->index('estado');
            $table->index('fecha_inicio');
            $table->index('codigo_grupo');
            $table->index('reserva_id');
            $table->index(['fecha_inicio', 'estado']);
        });

        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            // === RELACIONES PRINCIPALES ===
            $table->foreignId('reserva_id')
                ->constrained('reservas')
                ->onDelete('cascade');           // Si se elimina la reserva → se eliminan sus pagos

            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->onDelete('restrict');

            // Opcional: Vinculación con la Biblia / Grupo
            $table->foreignId('grupo_id')
                ->nullable()
                ->constrained('grupos')
                ->onDelete('set null');

            // Opcional: Si el pago es a un proveedor (pago de la agencia)
            $table->foreignId('proveedor_id')
                ->nullable()
                ->constrained('proveedores')
                ->onDelete('set null');

            // === DATOS DEL PAGO ===
            $table->string('codigo_pago', 30)->unique();                    // Ej: PAGO-202603-001

            $table->decimal('monto', 12, 2);
            $table->enum('moneda', ['PEN', 'USD'])->default('PEN');

            $table->enum('tipo_pago', [
                'inicial',      // Depósito inicial / Seña
                'parcial',      // Pago a cuenta
                'final',        // Pago final
                'proveedor',    // Pago realizado a proveedor
                'devolucion',   // Reembolso al cliente
                'otro'
            ])->default('parcial');

            $table->enum('metodo_pago', [
                'transferencia_bancaria',
                'yape',
                'plin',
                'efectivo',
                'tarjeta_credito',
                'tarjeta_debito',
                'paypal',
                'otro'
            ]);

            $table->string('numero_operacion', 60)->nullable();   // Número de operación, código Yape, etc.
            $table->date('fecha_pago');
            $table->timestamp('fecha_registro')->useCurrent();

            $table->string('banco_origen', 100)->nullable();
            $table->string('banco_destino', 100)->nullable();

            $table->enum('estado', ['pendiente', 'confirmado', 'rechazado', 'devuelto'])
                ->default('confirmado');

            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->foreign('registrado_por')->references('id')->on('admins')->onDelete('set null');

            $table->text('notas')->nullable();

            $table->timestamps();   // created_at, updated_at

            // === ÍNDICES ===
            $table->index('reserva_id');
            $table->index('cliente_id');
            $table->index('grupo_id');
            $table->index('estado');
            $table->index('fecha_pago');
            $table->index('tipo_pago');
            $table->index(['reserva_id', 'estado']);
        });





    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('pasajeros');
        Schema::dropIfExists('grupos');
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('tipo_proveedores');
        Schema::dropIfExists('tour_availability');
        Schema::dropIfExists('tour_calendar_years');
        Schema::dropIfExists('tour_precios');
        Schema::dropIfExists('categoria_tour');
        Schema::dropIfExists('agentes');
        Schema::dropIfExists('tours');
        Schema::dropIfExists('cliente_dieta');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('dietas');
        Schema::dropIfExists('idiomas');
        Schema::dropIfExists('paises');
        Schema::dropIfExists('admins');
    }
};
