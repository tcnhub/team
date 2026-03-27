<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Tour;
use App\Models\TourCalendarYear;
use App\Models\TourAvailability;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourCalendarController extends Controller
{
    /**
     * Mostrar formulario para agregar un año de calendario al tour
     */
    public function create(Tour $tour)
    {
        $aniosExistentes = TourCalendarYear::where('tour_id', $tour->id)
            ->pluck('anio');

        return view('admin.tours.calendar.create', compact('tour', 'aniosExistentes'));
    }

    /**
     * Generar todos los días del año para el tour
     */
    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'anio'           => [
                'required',
                'integer',
                'min:2020',
                'max:2100',
                function ($attribute, $value, $fail) use ($tour) {
                    if (TourCalendarYear::where('tour_id', $tour->id)->where('anio', $value)->exists()) {
                        $fail("El año {$value} ya está generado para este tour.");
                    }
                },
            ],
            'capacidad_anio' => 'nullable|integer|min:1|max:' . $tour->capacidad_maxima,
        ]);

        try {
            DB::beginTransaction();

            $calendar = TourCalendarYear::create([
                'tour_id'        => $tour->id,
                'anio'           => $validated['anio'],
                'capacidad_anio' => $validated['capacidad_anio'] ?? null,
            ]);

            $capacidadDia = $validated['capacidad_anio'] ?? $tour->capacidad_diaria;
            $anio         = $validated['anio'];
            $esBisiesto   = (($anio % 4 === 0 && $anio % 100 !== 0) || ($anio % 400 === 0));
            $totalDias    = $esBisiesto ? 366 : 365;

            $fecha = Carbon::create($anio, 1, 1);
            $rows  = [];

            for ($i = 0; $i < $totalDias; $i++) {
                $rows[] = [
                    'tour_id'            => $tour->id,
                    'calendar_year_id'   => $calendar->id,
                    'fecha'              => $fecha->toDateString(),
                    'capacidad_dia'      => $capacidadDia,
                    'espacios_usados'    => 0,
                    'espacios_bloqueados'=> 0,
                ];
                $fecha->addDay();
            }

            // Insertar en lotes de 100
            foreach (array_chunk($rows, 100) as $chunk) {
                DB::table('tour_availability')->insert($chunk);
            }

            DB::commit();

            return redirect()
                ->route('admin.tours.calendar.show', [$tour, $calendar])
                ->with('success', "Calendario {$anio} generado con {$totalDias} días.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar el calendario: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar el calendario de un año (vista grilla mensual)
     */
    public function show(Request $request, Tour $tour, TourCalendarYear $calendar)
    {
        abort_unless($calendar->tour_id === $tour->id, 404);

        $mesFiltro = (int) $request->get('mes', 0);

        $query = TourAvailability::where('calendar_year_id', $calendar->id)
            ->orderBy('fecha');

        if ($mesFiltro >= 1 && $mesFiltro <= 12) {
            $query->whereMonth('fecha', $mesFiltro);
        }

        $dias = $query->get();

        // Agrupar por mes
        $diasPorMes = $dias->groupBy(fn($d) => $d->fecha->month);

        return view('admin.tours.calendar.show', compact('tour', 'calendar', 'diasPorMes', 'mesFiltro'));
    }

    /**
     * Eliminar un año de calendario (y todos sus días)
     */
    public function destroy(Tour $tour, TourCalendarYear $calendar)
    {
        abort_unless($calendar->tour_id === $tour->id, 404);

        $calendar->delete();

        return redirect()
            ->route('admin.tours.show', $tour)
            ->with('success', "Calendario {$calendar->anio} eliminado.");
    }

    /**
     * Actualizar un día específico de disponibilidad (AJAX o form normal)
     */
    public function updateDay(Request $request, Tour $tour, TourCalendarYear $calendar, TourAvailability $availability)
    {
        abort_unless($availability->tour_id === $tour->id, 404);

        $validated = $request->validate([
            'espacios_bloqueados' => 'required|integer|min:0|max:' . ($availability->capacidad_dia - $availability->espacios_usados),
        ]);

        $availability->update(['espacios_bloqueados' => $validated['espacios_bloqueados']]);

        if ($request->expectsJson()) {
            // Recargar para obtener columnas calculadas actualizadas
            $availability->refresh();

            return response()->json([
                'ok'                   => true,
                'espacios_disponibles' => $availability->espacios_disponibles,
                'espacios_bloqueados'  => $availability->espacios_bloqueados,
                'disponible'           => $availability->disponible,
            ]);
        }

        return back()->with('success', 'Disponibilidad actualizada.');
    }

    /**
     * Calendario de reservas por tour (mes por mes, todo el año)
     */
    public function reservasCalendario(Request $request, Tour $tour)
    {
        $anio = (int) $request->get('anio', now()->year);

        // Todas las reservas de este tour en el año seleccionado
        $reservas = Reserva::with(['cliente', 'agente'])
            ->where(function ($q) use ($tour) {
                $q->whereHas('availability', fn($sq) => $sq->where('tour_id', $tour->id))
                  ->orWhere(function ($sq) use ($tour) {
                      // Reservas sin availability_id pero vinculadas al tour por descripcion_servicio (fallback no requerido)
                  });
            })
            ->whereYear('fecha_inicio', $anio)
            ->whereNotIn('estado_reserva', ['cancelada', 'reembolsada'])
            ->orderBy('fecha_inicio')
            ->get();

        // Calcular fecha_fin real para cada reserva
        $reservas->each(function ($reserva) use ($tour) {
            if (! $reserva->fecha_fin) {
                $dias = $tour->duracion_dias ?? 1;
                $reserva->fecha_fin_calculada = $reserva->fecha_inicio->copy()->addDays($dias - 1);
            } else {
                $reserva->fecha_fin_calculada = $reserva->fecha_fin;
            }
        });

        // Construir mapa: fecha => [reservas]
        $mapaFechas = [];
        foreach ($reservas as $reserva) {
            $cursor = $reserva->fecha_inicio->copy();
            $fin    = $reserva->fecha_fin_calculada;
            while ($cursor->lte($fin)) {
                $key = $cursor->format('Y-m-d');
                $mapaFechas[$key][] = $reserva;
                $cursor->addDay();
            }
        }

        // Años disponibles para el selector
        $aniosDisponibles = collect(range(now()->year - 1, now()->year + 2));

        return view('admin.tours.reservas-calendario', compact(
            'tour', 'reservas', 'mapaFechas', 'anio', 'aniosDisponibles'
        ));
    }

    /**
     * Actualización masiva de disponibilidad (bulk update por mes/rango)
     */
    public function bulkUpdate(Request $request, Tour $tour, TourCalendarYear $calendar)
    {
        abort_unless($calendar->tour_id === $tour->id, 404);

        $validated = $request->validate([
            'mes'                 => 'required|integer|min:1|max:12',
            'espacios_bloqueados' => 'required|integer|min:0',
        ]);

        TourAvailability::where('calendar_year_id', $calendar->id)
            ->whereMonth('fecha', $validated['mes'])
            ->update(['espacios_bloqueados' => $validated['espacios_bloqueados']]);

        return back()->with('success', 'Disponibilidad actualizada para el mes seleccionado.');
    }
}
