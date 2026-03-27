<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pais;
use App\Models\Idioma;
use App\Models\Dieta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::with(['pais', 'idioma', 'dieta'])
            ->latest();

        // Filtros
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                    ->orWhere('apellido', 'like', "%{$request->buscar}%")
                    ->orWhere('nombre_completo', 'like', "%{$request->buscar}%")
                    ->orWhere('numero_documento', 'like', "%{$request->buscar}%")
                    ->orWhere('email', 'like', "%{$request->buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        if ($request->filled('pais_id')) {
            $query->where('pais_id', $request->pais_id);
        }

        $clientes = $query->paginate(20);

        $paises = Pais::orderBy('nombre')->get();

        return view('admin.clientes.index', compact('clientes', 'paises'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $paises = Pais::orderBy('nombre')->get();
        $idiomas = Idioma::orderBy('nombre')->get();
        $dietas = Dieta::orderBy('nombre')->get();

        return view('admin.clientes.create', compact('paises', 'idiomas', 'dietas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'                => 'required|string|max:255',
            'apellido'              => 'required|string|max:255',
            'tipo_documento'        => ['required', Rule::in(['passport', 'dni', 'id'])],
            'numero_documento'      => 'required|string|unique:clientes,numero_documento',
            'pais_id'               => 'nullable|exists:paises,id',
            'idioma_id'             => 'nullable|exists:idiomas,id',
            'dieta_id'              => 'nullable|exists:dietas,id',
            'email'                 => 'nullable|email|unique:clientes,email',
            'telefono'              => 'nullable|string|max:20',
            'whatsapp'              => 'nullable|string|max:20',
            'fecha_nacimiento'      => 'nullable|date',
            'genero'                => ['nullable', Rule::in(['male', 'female', 'other'])],
            'notas_medicas'         => 'nullable|string',
            'pasaporte_expiracion'  => 'nullable|date',
            'contacto_emergencia'   => 'nullable|string|max:255',
            'telefono_emergencia'   => 'nullable|string|max:20',
            'activo'                => 'nullable|boolean',   // ← Corregido
        ]);

        // Convertir correctamente el valor del checkbox
        $validated['activo'] = $request->boolean('activo');

        // Generar nombre_completo automáticamente
        $validated['nombre_completo'] = trim($validated['nombre'] . ' ' . $validated['apellido']);

        $cliente = Cliente::create($validated);

        return redirect()
            ->route('admin.clientes.show', $cliente)
            ->with('success', 'Cliente creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $cliente)
    {
        $cliente->load(['pais', 'idioma', 'dieta']);
        return view('admin.clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        $paises = Pais::orderBy('nombre')->get();
        $idiomas = Idioma::orderBy('nombre')->get();
        $dietas = Dieta::orderBy('nombre')->get();

        return view('admin.clientes.edit', compact('cliente', 'paises', 'idiomas', 'dietas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre'                => 'required|string|max:255',
            'apellido'              => 'required|string|max:255',
            'tipo_documento'        => ['required', Rule::in(['passport', 'dni', 'id'])],
            'numero_documento'      => 'required|string|unique:clientes,numero_documento,' . $cliente->id,
            'pais_id'               => 'nullable|exists:paises,id',
            'idioma_id'             => 'nullable|exists:idiomas,id',
            'dieta_id'              => 'nullable|exists:dietas,id',
            'email'                 => 'nullable|email|unique:clientes,email,' . $cliente->id,
            'telefono'              => 'nullable|string|max:20',
            'whatsapp'              => 'nullable|string|max:20',
            'fecha_nacimiento'      => 'nullable|date',
            'genero'                => ['nullable', Rule::in(['male', 'female', 'other'])],
            'notas_medicas'         => 'nullable|string',
            'pasaporte_expiracion'  => 'nullable|date',
            'contacto_emergencia'   => 'nullable|string|max:255',
            'telefono_emergencia'   => 'nullable|string|max:20',
            'activo'                => 'nullable|boolean',   // ← Corregido
        ]);

        // Convertir correctamente el valor del checkbox
        $validated['activo'] = $request->boolean('activo');

        $validated['nombre_completo'] = trim($validated['nombre'] . ' ' . $validated['apellido']);

        $cliente->update($validated);

        return redirect()
            ->route('admin.clientes.show', $cliente)   // ← Corregido (quitado admin.admin)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        // Evitar eliminar cliente si tiene reservas activas
        if ($cliente->reservas()->whereIn('estado_reserva', ['pendiente', 'confirmada', 'pagada'])->exists()) {
            return back()->with('error', 'No se puede eliminar el cliente porque tiene reservas activas.');
        }

        $cliente->delete(); // Soft delete

        return redirect()
            ->route('admin.clientes.index')   // ← Corregido (quitado admin.admin)
            ->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * Método adicional: Buscar cliente por documento (útil para AJAX en reservas)
     */
    public function buscarPorDocumento(Request $request)
    {
        $request->validate([
            'numero_documento' => 'required|string'
        ]);

        $cliente = Cliente::where('numero_documento', $request->numero_documento)
            ->with(['pais', 'idioma'])
            ->first();

        if (!$cliente) {
            return response()->json(['encontrado' => false]);
        }

        return response()->json([
            'encontrado' => true,
            'cliente' => $cliente
        ]);
    }
}
