<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Agente::query();

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombres', 'like', "%{$request->buscar}%")
                    ->orWhere('apellidos', 'like', "%{$request->buscar}%")
                    ->orWhere('email', 'like', "%{$request->buscar}%")
                    ->orWhere('codigo_agente', 'like', "%{$request->buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado === '1');
        }

        $agentes = $query->latest()->paginate(15);

        return view('admin.agentes.index', compact('agentes'));
    }

    public function create()
    {
        return view('admin.agentes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_agente'       => 'nullable|string|max:20|unique:agentes,codigo_agente',
            'nombres'             => 'required|string|max:255',
            'apellidos'           => 'required|string|max:255',
            'email'               => 'required|email|unique:agentes,email',
            'telefono'            => 'nullable|string|max:20',
            'celular'             => 'required|string|max:20',
            'dni'                 => 'nullable|string|max:20|unique:agentes,dni',
            'fecha_nacimiento'    => 'nullable|date',
            'direccion'           => 'nullable|string|max:255',
            'ciudad'              => 'nullable|string|max:100',
            'pais'                => 'nullable|string|max:100',
            'genero'              => ['nullable', Rule::in(['masculino', 'femenino', 'otro'])],
            'estado'              => ['nullable', 'boolean'],
            'fecha_ingreso'       => 'nullable|date',
            'fecha_salida'        => 'nullable|date|after_or_equal:fecha_ingreso',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'departamento'        => 'nullable|string|max:100',
            'notas'               => 'nullable|string',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        Agente::create($validated);

        return redirect()
            ->route('admin.agentes.index')
            ->with('success', 'Agente creado correctamente.');
    }

    public function show(Agente $agente)
    {
        $agente->load('reservas');
        return view('admin.agentes.show', compact('agente'));
    }

    public function edit(Agente $agente)
    {
        return view('admin.agentes.edit', compact('agente'));
    }

    public function update(Request $request, Agente $agente)
    {
        $validated = $request->validate([
            'codigo_agente'       => 'nullable|string|max:20|unique:agentes,codigo_agente,' . $agente->id,
            'nombres'             => 'required|string|max:255',
            'apellidos'           => 'required|string|max:255',
            'email'               => 'required|email|unique:agentes,email,' . $agente->id,
            'telefono'            => 'nullable|string|max:20',
            'celular'             => 'required|string|max:20',
            'dni'                 => 'nullable|string|max:20|unique:agentes,dni,' . $agente->id,
            'fecha_nacimiento'    => 'nullable|date',
            'direccion'           => 'nullable|string|max:255',
            'ciudad'              => 'nullable|string|max:100',
            'pais'                => 'nullable|string|max:100',
            'genero'              => ['nullable', Rule::in(['masculino', 'femenino', 'otro'])],
            'estado'              => ['nullable', 'boolean'],
            'fecha_ingreso'       => 'nullable|date',
            'fecha_salida'        => 'nullable|date|after_or_equal:fecha_ingreso',
            'comision_porcentaje' => 'nullable|numeric|min:0|max:100',
            'departamento'        => 'nullable|string|max:100',
            'notas'               => 'nullable|string',
        ]);

        $validated['estado'] = $request->boolean('estado', true);

        $agente->update($validated);

        return redirect()
            ->route('admin.agentes.show', $agente)
            ->with('success', 'Agente actualizado correctamente.');
    }

    public function destroy(Agente $agente)
    {
        if ($agente->reservas()->whereIn('estado_reserva', ['pendiente', 'confirmada', 'pagada'])->exists()) {
            return back()->with('error', 'No se puede eliminar el agente porque tiene reservas activas.');
        }

        $agente->delete();

        return redirect()
            ->route('admin.agentes.index')
            ->with('success', 'Agente eliminado correctamente.');
    }
}
