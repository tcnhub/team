<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;

class AddonController extends Controller
{
    public function index(Request $request)
    {
        $query = Addon::query()->latest();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%')
                ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
        }

        $addons = $query->paginate(15)->withQueryString();

        return view('admin.addons.index', compact('addons'));
    }

    public function create()
    {
        return view('admin.addons.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateAddon($request);
        Addon::create($validated);

        return redirect()->route('admin.addons.index')->with('success', 'Addon creado correctamente.');
    }

    public function show(Addon $addon)
    {
        $addon->load(['tours', 'reservas']);

        return view('admin.addons.show', compact('addon'));
    }

    public function edit(Addon $addon)
    {
        return view('admin.addons.edit', compact('addon'));
    }

    public function update(Request $request, Addon $addon)
    {
        $validated = $this->validateAddon($request, $addon->id);
        $addon->update($validated);

        return redirect()->route('admin.addons.index')->with('success', 'Addon actualizado correctamente.');
    }

    public function destroy(Addon $addon)
    {
        $addon->delete();

        return redirect()->route('admin.addons.index')->with('success', 'Addon eliminado correctamente.');
    }

    private function validateAddon(Request $request, ?int $addonId = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255|unique:addons,nombre,' . $addonId,
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
        ]);
    }
}
