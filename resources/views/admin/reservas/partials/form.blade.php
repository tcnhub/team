<form action="{{ isset($reserva) ? route('admin.reservas.update', $reserva) : route('admin.reservas.store') }}"
      method="POST">

    @csrf
    @if(isset($reserva) && $reserva->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <!-- Código de Reserva (solo lectura en edición) -->
        <div class="col-md-3">
            <label class="form-label">Código de Reserva</label>
            <input type="text"
                   class="form-control bg-light"
                   value="{{ isset($reserva) ? $reserva->codigo_reserva : 'Se generará automáticamente' }}"
                   readonly>
        </div>

        <!-- Cliente -->
        <div class="col-md-6">
            <label class="form-label">Cliente <span class="text-danger">*</span></label>
            <select name="id_cliente" class="form-select @error('id_cliente') is-invalid @enderror" required>
                <option value="">Seleccionar cliente...</option>
                @foreach($clientes ?? [] as $cliente)
                    <option value="{{ $cliente->id }}"
                        {{ (isset($reserva) && $reserva->id_cliente == $cliente->id) || old('id_cliente') == $cliente->id ? 'selected' : '' }}>
                        {{ $cliente->nombre_completo ?? $cliente->nombre . ' ' . $cliente->apellido }}
                        ({{ $cliente->numero_documento ?? 'Sin documento' }})
                    </option>
                @endforeach
            </select>
            @error('id_cliente')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Agente (Opcional) -->
        <div class="col-md-3">
            <label class="form-label">Agente</label>
            <select name="id_agente" class="form-select @error('id_agente') is-invalid @enderror">
                <option value="">Sin agente</option>
                @foreach($agentes ?? [] as $agente)
                    <option value="{{ $agente->id }}"
                        {{ (isset($reserva) && $reserva->id_agente == $agente->id) || old('id_agente') == $agente->id ? 'selected' : '' }}>
                        {{ $agente->nombre_completo ?? $agente->nombres . ' ' . $agente->apellidos }}
                    </option>
                @endforeach
            </select>
            @error('id_agente')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tipo de Reserva -->
        <div class="col-md-4">
            <label class="form-label">Tipo de Reserva <span class="text-danger">*</span></label>
            <input type="text" name="tipo_reserva"
                   class="form-control @error('tipo_reserva') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->tipo_reserva : old('tipo_reserva') }}"
                   placeholder="Tour, Paquete, Hotel, Vuelo..." required>
            @error('tipo_reserva')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Descripción del Servicio -->
        <div class="col-md-8">
            <label class="form-label">Descripción del Servicio</label>
            <input type="text" name="descripcion_servicio"
                   class="form-control @error('descripcion_servicio') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->descripcion_servicio : old('descripcion_servicio') }}"
                   placeholder="Machu Picchu 4D/3N - Marzo 2026">
            @error('descripcion_servicio')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fechas del Viaje -->
        <div class="col-md-3">
            <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
            <input type="date" name="fecha_inicio"
                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->fecha_inicio->format('Y-m-d') : old('fecha_inicio') }}" required>
            @error('fecha_inicio')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha de Fin</label>
            <input type="date" name="fecha_fin"
                   class="form-control @error('fecha_fin') is-invalid @enderror"
                   value="{{ isset($reserva) && $reserva->fecha_fin ? $reserva->fecha_fin->format('Y-m-d') : old('fecha_fin') }}">
            @error('fecha_fin')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Disponibilidad (opcional) -->
        <div class="col-md-6">
            <label class="form-label">Disponibilidad del Tour (Opcional)</label>
            <select name="availability_id" class="form-select @error('availability_id') is-invalid @enderror">
                <option value="">No vincular con disponibilidad específica</option>
                @foreach($availabilities ?? [] as $availability)
                    <option value="{{ $availability->id }}"
                        {{ (isset($reserva) && $reserva->availability_id == $availability->id) || old('availability_id') == $availability->id ? 'selected' : '' }}>
                        {{ $availability->tour->nombre_tour ?? 'Tour' }} - {{ $availability->fecha->format('d/m/Y') }}
                        ({{ $availability->espacios_disponibles ?? 'N/A' }} disponibles)
                    </option>
                @endforeach
            </select>
            @error('availability_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Número de Pasajeros -->
        <div class="col-md-3">
            <label class="form-label">Total Pasajeros <span class="text-danger">*</span></label>
            <input type="number" name="num_pasajeros" min="1"
                   class="form-control @error('num_pasajeros') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->num_pasajeros : old('num_pasajeros', 1) }}" required>
            @error('num_pasajeros')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Adultos</label>
            <input type="number" name="num_adultos" min="0"
                   class="form-control @error('num_adultos') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->num_adultos : old('num_adultos', 1) }}">
            @error('num_adultos')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Niños</label>
            <input type="number" name="num_ninos" min="0"
                   class="form-control @error('num_ninos') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->num_ninos : old('num_ninos', 0) }}">
            @error('num_ninos')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Bebés</label>
            <input type="number" name="num_bebes" min="0"
                   class="form-control @error('num_bebes') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->num_bebes : old('num_bebes', 0) }}">
            @error('num_bebes')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Información Financiera -->
        <div class="col-md-4">
            <label class="form-label">Moneda</label>
            <select name="moneda" class="form-select @error('moneda') is-invalid @enderror">
                <option value="PEN" {{ (isset($reserva) && $reserva->moneda == 'PEN') || old('moneda') == 'PEN' ? 'selected' : '' }}>PEN - Soles</option>
                <option value="USD" {{ (isset($reserva) && $reserva->moneda == 'USD') || old('moneda') == 'USD' ? 'selected' : '' }}>USD - Dólares</option>
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Precio Total <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="precio_total"
                   class="form-control @error('precio_total') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->precio_total : old('precio_total') }}" required>
            @error('precio_total')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Descuento</label>
            <input type="number" step="0.01" name="descuento"
                   class="form-control @error('descuento') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->descuento : old('descuento', 0) }}">
            @error('descuento')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Notas y Requisitos -->
        <div class="col-12">
            <label class="form-label">Notas / Observaciones</label>
            <textarea name="notas" class="form-control" rows="3">{{ isset($reserva) ? $reserva->notas : old('notas') }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Requisitos Especiales</label>
            <textarea name="requisitos_especiales" class="form-control" rows="3">{{ isset($reserva) ? $reserva->requisitos_especiales : old('requisitos_especiales') }}</textarea>
        </div>

        <!-- Estado de la Reserva (solo visible en edición) -->
        @if(isset($reserva) && $reserva->exists)
            <div class="col-md-4">
                <label class="form-label">Estado de la Reserva</label>
                <select name="estado_reserva" class="form-select @error('estado_reserva') is-invalid @enderror">
                    <option value="pendiente" {{ $reserva->estado_reserva == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmada" {{ $reserva->estado_reserva == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                    <option value="pagada" {{ $reserva->estado_reserva == 'pagada' ? 'selected' : '' }}>Pagada</option>
                    <option value="cancelada" {{ $reserva->estado_reserva == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    <option value="reembolsada" {{ $reserva->estado_reserva == 'reembolsada' ? 'selected' : '' }}>Reembolsada</option>
                    <option value="completada" {{ $reserva->estado_reserva == 'completada' ? 'selected' : '' }}>Completada</option>
                </select>
                @error('estado_reserva')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endif

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> {{ isset($reserva) ? 'Actualizar Reserva' : 'Guardar Nueva Reserva' }}
        </button>
        <a href="{{ route('admin.reservas.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
