<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Dieta;
use App\Models\Idioma;
use App\Models\Pais;
use App\Models\Pasajero;
use App\Models\Reserva;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PasajeroController extends Controller
{
    public function index(Request $request)
    {
        $query = Pasajero::with(['cliente', 'reserva', 'tour', 'pais', 'idioma', 'dieta'])->latest();

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                    ->orWhere('apellido', 'like', '%' . $request->buscar . '%')
                    ->orWhere('nombre_completo', 'like', '%' . $request->buscar . '%')
                    ->orWhere('numero_documento', 'like', '%' . $request->buscar . '%')
                    ->orWhere('email', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('estado')) {
            $query->where('activo', $request->estado === 'activo');
        }

        if ($request->filled('tipo_documento')) {
            $query->where('tipo_documento', $request->tipo_documento);
        }

        if ($request->filled('pais_id')) {
            $query->where('pais_id', $request->pais_id);
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('reserva_id')) {
            $query->where('reserva_id', $request->reserva_id);
        }

        if ($request->filled('tour_id')) {
            $query->where('tour_id', $request->tour_id);
        }

        $pasajeros = $query->paginate(20)->withQueryString();
        $paises = Pais::orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre_completo')->get(['id', 'nombre_completo']);
        $reservas = Reserva::orderByDesc('id')->get(['id', 'codigo_reserva']);
        $tours = Tour::orderBy('nombre_tour')->get(['id', 'nombre_tour']);

        return view('admin.pasajeros.index', compact('pasajeros', 'paises', 'clientes', 'reservas', 'tours'));
    }

    public function create()
    {
        return view('admin.pasajeros.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validatePasajero($request);
        $validated['activo'] = $request->boolean('activo');
        $validated['tipo_pasajero'] = $validated['tipo_pasajero'] ?? 'adulto';
        $validated['nombre_completo'] = trim($validated['nombre'] . ' ' . $validated['apellido']);

        $pasajero = Pasajero::create($validated);

        return redirect()
            ->route('admin.pasajeros.show', $pasajero)
            ->with('success', 'Pasajero creado correctamente.');
    }

    public function show(Pasajero $pasajero)
    {
        $pasajero->load(['cliente', 'reserva', 'tour', 'pais', 'idioma', 'dieta']);

        return view('admin.pasajeros.show', compact('pasajero'));
    }

    public function edit(Pasajero $pasajero)
    {
        return view('admin.pasajeros.edit', array_merge($this->formData(), compact('pasajero')));
    }

    public function reservaRelacion(Reserva $reserva)
    {
        $reserva->load(['cliente:id,nombre_completo,numero_documento', 'tour:id,nombre_tour']);

        return response()->json([
            'reserva' => [
                'id' => $reserva->id,
                'codigo_reserva' => $reserva->codigo_reserva,
            ],
            'cliente' => $reserva->cliente ? [
                'id' => $reserva->cliente->id,
                'nombre_completo' => $reserva->cliente->nombre_completo,
                'numero_documento' => $reserva->cliente->numero_documento,
            ] : null,
            'tour' => $reserva->tour ? [
                'id' => $reserva->tour->id,
                'nombre_tour' => $reserva->tour->nombre_tour,
            ] : null,
        ]);
    }

    public function update(Request $request, Pasajero $pasajero)
    {
        $validated = $this->validatePasajero($request, $pasajero->id);
        $validated['activo'] = $request->boolean('activo');
        $validated['tipo_pasajero'] = $validated['tipo_pasajero'] ?? 'adulto';
        $validated['nombre_completo'] = trim($validated['nombre'] . ' ' . $validated['apellido']);

        $pasajero->update($validated);

        return redirect()
            ->route('admin.pasajeros.show', $pasajero)
            ->with('success', 'Pasajero actualizado correctamente.');
    }

    public function destroy(Pasajero $pasajero)
    {
        $pasajero->delete();

        return redirect()
            ->route('admin.pasajeros.index')
            ->with('success', 'Pasajero eliminado correctamente.');
    }

    private function formData(): array
    {
        return [
            'clientes' => Cliente::orderBy('nombre_completo')->get(['id', 'nombre_completo', 'numero_documento']),
            'reservas' => Reserva::with('cliente')->orderByDesc('id')->get(['id', 'codigo_reserva', 'id_cliente', 'tour_id']),
            'tours' => Tour::orderBy('nombre_tour')->get(['id', 'nombre_tour']),
            'paises' => Pais::orderBy('nombre')->get(),
            'idiomas' => Idioma::orderBy('nombre')->get(),
            'dietas' => Dieta::orderBy('nombre')->get(),
        ];
    }

    private function validatePasajero(Request $request, ?int $pasajeroId = null): array
    {
        return $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'reserva_id' => ['required', 'exists:reservas,id'],
            'tour_id' => ['required', 'exists:tours,id'],
            'tipo_pasajero' => ['nullable', Rule::in(['adulto', 'estudiante', 'nino'])],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'tipo_documento' => ['required', Rule::in(['passport', 'dni', 'id'])],
            'numero_documento' => ['required', 'string', 'max:255', 'unique:pasajeros,numero_documento,' . $pasajeroId],
            'pais_id' => ['nullable', 'exists:paises,id'],
            'idioma_id' => ['nullable', 'exists:idiomas,id'],
            'dieta_id' => ['nullable', 'exists:dietas,id'],
            'email' => ['nullable', 'email', 'unique:pasajeros,email,' . $pasajeroId],
            'telefono' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'genero' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'notas_medicas' => ['nullable', 'string'],
            'pasaporte_expiracion' => ['nullable', 'date'],
            'contacto_emergencia' => ['nullable', 'string', 'max:255'],
            'telefono_emergencia' => ['nullable', 'string', 'max:20'],
            'activo' => ['nullable', 'boolean'],
        ]);
    }
}
