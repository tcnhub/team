<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourPrecio;
use Illuminate\Http\Request;

class TourPriceController extends Controller
{
    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'etiqueta'      => 'required|string|max:100',
            'precio'        => 'required|numeric|min:0',
            'descripcion'   => 'nullable|string',
            'min_personas'  => 'nullable|integer|min:1',
            'max_personas'  => 'nullable|integer|min:1',
            'es_predeterminado' => 'boolean',
        ]);

        // Si se marca como predeterminado, quitarlo de los demás
        if ($validated['es_predeterminado']) {
            $tour->precios()->update(['es_predeterminado' => false]);
        }

        $tour->precios()->create($validated);

        return back()->with('success', 'Precio agregado correctamente.');
    }

    public function update(Request $request, Tour $tour, TourPrecio $price)
    {
        $validated = $request->validate([
            'etiqueta'      => 'required|string|max:100',
            'precio'        => 'required|numeric|min:0',
            'descripcion'   => 'nullable|string',
            'min_personas'  => 'nullable|integer',
            'max_personas'  => 'nullable|integer',
            'es_predeterminado' => 'boolean',
        ]);

        if ($validated['es_predeterminado']) {
            $tour->precios()->where('id', '!=', $price->id)
                ->update(['es_predeterminado' => false]);
        }

        $price->update($validated);

        return back()->with('success', 'Precio actualizado.');
    }

    public function destroy(Tour $tour, TourPrecio $price)
    {
        $price->delete();
        return back()->with('success', 'Precio eliminado.');
    }
}
