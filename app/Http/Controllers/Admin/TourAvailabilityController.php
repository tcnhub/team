<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourAvailabilityController extends Controller
{
    public function index(Tour $tour)
    {
        $tour->load(['calendarYears', 'availability']);
        return view('admin.tours.availability.index', compact('tour'));
    }

    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'fecha'           => 'required|date|after_or_equal:today',
            'capacidad_dia'   => 'required|integer|min:1',
            'espacios_bloqueados' => 'nullable|integer|min:0',
        ]);

        // Aquí puedes crear o actualizar la disponibilidad
        // (lógica más avanzada se puede expandir después)

        return back()->with('success', 'Disponibilidad actualizada.');
    }

    public function update(Request $request, Tour $tour, $availability)
    {
        // Lógica para actualizar un día específico
    }
}
