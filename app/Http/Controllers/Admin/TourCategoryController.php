<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourCategoryController extends Controller
{
    public function attach(Request $request, Tour $tour)
    {
        $request->validate(['categoria_id' => 'required|exists:categorias,id']);
        $tour->categorias()->attach($request->categoria_id);

        return back()->with('success', 'Categoría agregada.');
    }

    public function detach(Tour $tour, $categoria)
    {
        $tour->categorias()->detach($categoria);
        return back()->with('success', 'Categoría removida.');
    }
}
