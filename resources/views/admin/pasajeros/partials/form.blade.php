<form action="{{ isset($pasajero) ? route('admin.pasajeros.update', $pasajero) : route('admin.pasajeros.store') }}" method="POST">
    @csrf
    @if(isset($pasajero) && $pasajero->exists)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Reserva <span class="text-danger">*</span></label>
            <select name="reserva_id" id="reserva_id" class="form-select @error('reserva_id') is-invalid @enderror" required>
                <option value="">Seleccionar reserva...</option>
                @foreach($reservas ?? [] as $reserva)
                    <option value="{{ $reserva->id }}" {{ old('reserva_id', $pasajero->reserva_id ?? '') == $reserva->id ? 'selected' : '' }}>
                        {{ $reserva->codigo_reserva }} - {{ $reserva->cliente?->nombre_completo ?? 'Sin cliente' }}
                    </option>
                @endforeach
            </select>
            @error('reserva_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Al elegir la reserva se cargarán automáticamente el cliente y el tour.</div>
        </div>

        <div class="col-md-3">
            <label class="form-label">Cliente <span class="text-danger">*</span></label>
            <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                <option value="">Seleccionar cliente...</option>
                @foreach($clientes ?? [] as $cliente)
                    <option value="{{ $cliente->id }}" {{ old('cliente_id', $pasajero->cliente_id ?? '') == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre_completo }} ({{ $cliente->numero_documento }})
                    </option>
                @endforeach
            </select>
            @error('cliente_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Tour <span class="text-danger">*</span></label>
            <select name="tour_id" id="tour_id" class="form-select @error('tour_id') is-invalid @enderror" required>
                <option value="">Seleccionar tour...</option>
                @foreach($tours ?? [] as $tour)
                    <option value="{{ $tour->id }}" {{ old('tour_id', $pasajero->tour_id ?? '') == $tour->id ? 'selected' : '' }}>
                        {{ $tour->nombre_tour }}
                    </option>
                @endforeach
            </select>
            @error('tour_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ old('nombre', $pasajero->nombre ?? '') }}" required>
            @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Apellido <span class="text-danger">*</span></label>
            <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror"
                   value="{{ old('apellido', $pasajero->apellido ?? '') }}" required>
            @error('apellido')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
            <select name="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" required>
                <option value="">Seleccionar...</option>
                <option value="passport" {{ old('tipo_documento', $pasajero->tipo_documento ?? '') === 'passport' ? 'selected' : '' }}>Passport</option>
                <option value="dni" {{ old('tipo_documento', $pasajero->tipo_documento ?? '') === 'dni' ? 'selected' : '' }}>DNI</option>
                <option value="id" {{ old('tipo_documento', $pasajero->tipo_documento ?? '') === 'id' ? 'selected' : '' }}>ID</option>
            </select>
            @error('tipo_documento')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
            <input type="text" name="numero_documento" class="form-control @error('numero_documento') is-invalid @enderror"
                   value="{{ old('numero_documento', $pasajero->numero_documento ?? '') }}" required>
            @error('numero_documento')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">País</label>
            <select name="pais_id" class="form-select @error('pais_id') is-invalid @enderror">
                <option value="">Seleccionar país...</option>
                @foreach($paises ?? [] as $pais)
                    <option value="{{ $pais->id }}" {{ old('pais_id', $pasajero->pais_id ?? '') == $pais->id ? 'selected' : '' }}>
                        {{ $pais->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Idioma</label>
            <select name="idioma_id" class="form-select @error('idioma_id') is-invalid @enderror">
                <option value="">Seleccionar idioma...</option>
                @foreach($idiomas ?? [] as $idioma)
                    <option value="{{ $idioma->id }}" {{ old('idioma_id', $pasajero->idioma_id ?? '') == $idioma->id ? 'selected' : '' }}>
                        {{ $idioma->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Dieta</label>
            <select name="dieta_id" class="form-select @error('dieta_id') is-invalid @enderror">
                <option value="">Sin preferencia</option>
                @foreach($dietas ?? [] as $dieta)
                    <option value="{{ $dieta->id }}" {{ old('dieta_id', $pasajero->dieta_id ?? '') == $dieta->id ? 'selected' : '' }}>
                        {{ $dieta->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $pasajero->email ?? '') }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                   value="{{ old('telefono', $pasajero->telefono ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">WhatsApp</label>
            <input type="text" name="whatsapp" class="form-control @error('whatsapp') is-invalid @enderror"
                   value="{{ old('whatsapp', $pasajero->whatsapp ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">Fecha de Nacimiento</label>
            <input type="text" name="fecha_nacimiento" data-date-format="Y-m-d"
                   class="form-control flatpickr-date @error('fecha_nacimiento') is-invalid @enderror"
                   value="{{ old('fecha_nacimiento', isset($pasajero) && $pasajero->fecha_nacimiento ? $pasajero->fecha_nacimiento->format('Y-m-d') : '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Género</label>
            <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                <option value="">Seleccionar...</option>
                <option value="male" {{ old('genero', $pasajero->genero ?? '') === 'male' ? 'selected' : '' }}>Masculino</option>
                <option value="female" {{ old('genero', $pasajero->genero ?? '') === 'female' ? 'selected' : '' }}>Femenino</option>
                <option value="other" {{ old('genero', $pasajero->genero ?? '') === 'other' ? 'selected' : '' }}>Otro</option>
            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label">Pasaporte Expiración</label>
            <input type="text" name="pasaporte_expiracion" data-date-format="Y-m-d"
                   class="form-control flatpickr-date @error('pasaporte_expiracion') is-invalid @enderror"
                   value="{{ old('pasaporte_expiracion', isset($pasajero) && $pasajero->pasaporte_expiracion ? $pasajero->pasaporte_expiracion->format('Y-m-d') : '') }}">
        </div>

        <div class="col-12">
            <label class="form-label">Notas Médicas</label>
            <textarea name="notas_medicas" class="form-control @error('notas_medicas') is-invalid @enderror" rows="3">{{ old('notas_medicas', $pasajero->notas_medicas ?? '') }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Contacto de Emergencia</label>
            <input type="text" name="contacto_emergencia" class="form-control @error('contacto_emergencia') is-invalid @enderror"
                   value="{{ old('contacto_emergencia', $pasajero->contacto_emergencia ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Teléfono de Emergencia</label>
            <input type="text" name="telefono_emergencia" class="form-control @error('telefono_emergencia') is-invalid @enderror"
                   value="{{ old('telefono_emergencia', $pasajero->telefono_emergencia ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox" name="activo" id="activo" value="1" class="form-check-input"
                       {{ old('activo', $pasajero->activo ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Pasajero activo</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> {{ isset($pasajero) ? 'Actualizar Pasajero' : 'Guardar Pasajero' }}
        </button>
        <a href="{{ route('admin.pasajeros.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reservaSelect = document.getElementById('reserva_id');
    const clienteSelect = document.getElementById('cliente_id');
    const tourSelect = document.getElementById('tour_id');
    const baseUrl = '{{ url('admin/pasajeros/reservas') }}';

    async function sincronizarReserva(reservaId) {
        if (!reservaId) {
            return;
        }

        try {
            const response = await fetch(`${baseUrl}/${reservaId}/relacion`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();

            if (data.cliente && clienteSelect) {
                clienteSelect.value = String(data.cliente.id);
            }

            if (data.tour && tourSelect) {
                tourSelect.value = String(data.tour.id);
            }
        } catch (error) {
            console.error('No se pudo sincronizar la reserva del pasajero.', error);
        }
    }

    if (reservaSelect) {
        reservaSelect.addEventListener('change', function () {
            sincronizarReserva(this.value);
        });

        if (reservaSelect.value) {
            sincronizarReserva(reservaSelect.value);
        }
    }
});
</script>
