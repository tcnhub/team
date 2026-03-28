<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index(Request $request)
    {
        $query = Configuracion::query()->latest();

        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%')
                ->orWhere('valor', 'like', '%' . $request->buscar . '%');
        }

        $configuraciones = $query->paginate(20)->withQueryString();

        return view('admin.configuraciones.index', compact('configuraciones'));
    }

    public function create()
    {
        return view('admin.configuraciones.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateConfiguracion($request);
        Configuracion::create($validated);

        return redirect()->route('admin.configuraciones.index')->with('success', 'Configuración creada correctamente.');
    }

    public function show(Configuracion $configuracione)
    {
        return view('admin.configuraciones.show', ['configuracion' => $configuracione]);
    }

    public function edit(Configuracion $configuracione)
    {
        return view('admin.configuraciones.edit', ['configuracion' => $configuracione]);
    }

    public function update(Request $request, Configuracion $configuracione)
    {
        $validated = $this->validateConfiguracion($request, $configuracione->id);
        $configuracione->update($validated);

        return redirect()->route('admin.configuraciones.index')->with('success', 'Configuración actualizada correctamente.');
    }

    public function destroy(Configuracion $configuracione)
    {
        $configuracione->delete();

        return redirect()->route('admin.configuraciones.index')->with('success', 'Configuración eliminada correctamente.');
    }

    private function validateConfiguracion(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255|unique:configuraciones,nombre,' . $id,
            'valor' => 'nullable|string',
        ]);
    }
}
