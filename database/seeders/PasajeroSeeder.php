<?php

namespace Database\Seeders;

use App\Models\Agente;
use App\Models\Cliente;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Tour;
use Illuminate\Database\Seeder;

class PasajeroSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = Cliente::orderBy('id')->take(10)->get();
        $tour = Tour::query()->where('estado', true)->first() ?? Tour::first();
        $agente = Agente::query()->where('estado', true)->first() ?? Agente::first();

        if (! $tour || $clientes->isEmpty()) {
            return;
        }

        foreach ($clientes as $index => $cliente) {
            $reserva = Reserva::firstOrCreate(
                ['codigo_reserva' => 'RES-PAS-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)],
                [
                    'estado_reserva' => 'confirmada',
                    'id_cliente' => $cliente->id,
                    'id_agente' => $agente?->id,
                    'tour_id' => $tour->id,
                    'tipo_reserva' => 'Tour',
                    'descripcion_servicio' => $tour->nombre_tour,
                    'fecha_inicio' => now()->addDays($index + 1)->toDateString(),
                    'fecha_fin' => now()->addDays($index + 2)->toDateString(),
                    'num_pasajeros' => 1,
                    'num_adultos' => 1,
                    'num_ninos' => 0,
                    'num_bebes' => 0,
                    'moneda' => 'USD',
                    'precio_total' => 100 + ($index * 10),
                    'descuento' => 0,
                    'precio_final' => 100 + ($index * 10),
                    'monto_pagado' => 0,
                    'fuente_reserva' => 'Seeder',
                ]
            );

            Pasajero::updateOrCreate(
                ['numero_documento' => 'PAX-' . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT)],
                [
                    'cliente_id' => $cliente->id,
                    'reserva_id' => $reserva->id,
                    'tour_id' => $tour->id,
                    'nombre' => $cliente->nombre,
                    'apellido' => $cliente->apellido,
                    'nombre_completo' => $cliente->nombre_completo,
                    'tipo_documento' => $cliente->tipo_documento,
                    'pais_id' => $cliente->pais_id,
                    'idioma_id' => $cliente->idioma_id,
                    'dieta_id' => $cliente->dieta_id,
                    'email' => 'pasajero' . ($index + 1) . '@demo.test',
                    'telefono' => $cliente->telefono,
                    'whatsapp' => $cliente->whatsapp,
                    'fecha_nacimiento' => $cliente->fecha_nacimiento,
                    'genero' => $cliente->genero,
                    'edad' => $cliente->edad,
                    'notas_medicas' => $cliente->notas_medicas,
                    'pasaporte_expiracion' => $cliente->pasaporte_expiracion,
                    'contacto_emergencia' => $cliente->contacto_emergencia,
                    'telefono_emergencia' => $cliente->telefono_emergencia,
                    'activo' => true,
                ]
            );
        }
    }
}
