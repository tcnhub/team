<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourAvailability;
use Illuminate\Http\Request;

class TourAvailabilityController extends Controller
{
    /**
     * Actualización rápida vía AJAX desde la grilla de calendario.
     * Recibe "disponibles_deseados" (espacios que el admin quiere dejar libres)
     * y calcula espacios_bloqueados = capacidad - usados - disponibles_deseados.
     */
    public function update(Request $request, Tour $tour, TourAvailability $availability)
    {
        abort_unless($availability->tour_id === $tour->id, 404);

        $maxDisponibles = $availability->capacidad_dia - $availability->espacios_usados;

        $validated = $request->validate([
            'disponibles_deseados' => "required|integer|min:0|max:{$maxDisponibles}",
        ]);

        $bloqueados = $maxDisponibles - $validated['disponibles_deseados'];
        $availability->update(['espacios_bloqueados' => $bloqueados]);
        $availability->refresh();

        return response()->json([
            'ok'                   => true,
            'espacios_disponibles' => $availability->espacios_disponibles,
            'espacios_bloqueados'  => $availability->espacios_bloqueados,
            'espacios_usados'      => $availability->espacios_usados,
            'disponible'           => $availability->disponible,
        ]);
    }
}
