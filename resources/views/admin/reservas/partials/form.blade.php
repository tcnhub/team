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
            <div class="input-group">
                <select name="id_cliente" id="selectCliente"
                        class="form-select @error('id_cliente') is-invalid @enderror" required>
                    <option value="">Seleccionar cliente...</option>
                    @foreach($clientes ?? [] as $cliente)
                        <option value="{{ $cliente->id }}"
                            {{ (isset($reserva) && $reserva->id_cliente == $cliente->id) || old('id_cliente') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre_completo ?? $cliente->nombre . ' ' . $cliente->apellido }}
                            ({{ $cliente->numero_documento ?? 'Sin documento' }})
                        </option>
                    @endforeach
                </select>
                <button type="button" class="btn btn-success" title="Crear nuevo cliente"
                        data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
                    <i class="ri-user-add-line"></i>
                </button>
                @error('id_cliente')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-text">
                <i class="ri-information-line me-1"></i>
                Si el cliente no está en la lista, usa el botón <strong>+</strong> para registrarlo.
            </div>
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

{{-- ── Modal: Crear Nuevo Cliente ── --}}
<div class="modal fade" id="modalNuevoCliente" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="ri-user-add-line me-2"></i>Registrar Nuevo Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="modalClienteAlerta" class="d-none"></div>

                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="mc_nombre"
                               class="form-control" placeholder="Nombres" required>
                        <div class="invalid-feedback" id="err_nombre"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" id="mc_apellido"
                               class="form-control" placeholder="Apellidos" required>
                        <div class="invalid-feedback" id="err_apellido"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Tipo Documento <span class="text-danger">*</span></label>
                        <select id="mc_tipo_documento" class="form-select">
                            <option value="passport">Passport</option>
                            <option value="dni">DNI</option>
                            <option value="id">ID / Otros</option>
                        </select>
                        <div class="invalid-feedback" id="err_tipo_documento"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">N° Documento <span class="text-danger">*</span></label>
                        <input type="text" id="mc_numero_documento"
                               class="form-control" placeholder="Número de documento">
                        <div class="invalid-feedback" id="err_numero_documento"></div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">País</label>
                        <select id="mc_pais_id" class="form-select">
                            <option value="">Seleccionar país...</option>
                            @foreach($paises ?? [] as $pais)
                                <option value="{{ $pais->id }}">{{ $pais->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" id="mc_email"
                               class="form-control" placeholder="correo@ejemplo.com">
                        <div class="invalid-feedback" id="err_email"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Teléfono / WhatsApp</label>
                        <input type="text" id="mc_telefono"
                               class="form-control" placeholder="+51 999 999 999">
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="btnGuardarCliente">
                    <span id="btnGuardarClienteSpinner" class="spinner-border spinner-border-sm me-1 d-none"></span>
                    <i class="ri-save-line me-1"></i>Guardar y Seleccionar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
(function () {
    const CSRF       = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    const STORE_URL  = '{{ route("admin.clientes.store-quick") }}';

    const fields = ['nombre', 'apellido', 'tipo_documento', 'numero_documento', 'pais_id', 'email', 'telefono'];

    function clearErrors() {
        fields.forEach(f => {
            const input = document.getElementById('mc_' + f);
            if (input) { input.classList.remove('is-invalid'); }
            const err = document.getElementById('err_' + f);
            if (err)  { err.textContent = ''; }
        });
        document.getElementById('modalClienteAlerta').className = 'd-none';
    }

    function showFieldError(field, msg) {
        const input = document.getElementById('mc_' + field);
        if (input) input.classList.add('is-invalid');
        const err = document.getElementById('err_' + field);
        if (err)  err.textContent = msg;
    }

    document.getElementById('btnGuardarCliente').addEventListener('click', async function () {
        clearErrors();

        const spinner = document.getElementById('btnGuardarClienteSpinner');
        spinner.classList.remove('d-none');
        this.disabled = true;

        const payload = {
            nombre           : document.getElementById('mc_nombre').value.trim(),
            apellido         : document.getElementById('mc_apellido').value.trim(),
            tipo_documento   : document.getElementById('mc_tipo_documento').value,
            numero_documento : document.getElementById('mc_numero_documento').value.trim(),
            pais_id          : document.getElementById('mc_pais_id').value || null,
            email            : document.getElementById('mc_email').value.trim() || null,
            telefono         : document.getElementById('mc_telefono').value.trim() || null,
        };

        try {
            const res  = await fetch(STORE_URL, {
                method : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept'      : 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (!res.ok) {
                // Errores de validación campo a campo
                if (res.status === 422 && data.errors) {
                    Object.entries(data.errors).forEach(([field, msgs]) => {
                        showFieldError(field, msgs[0]);
                    });
                } else {
                    const alerta = document.getElementById('modalClienteAlerta');
                    alerta.className = 'alert alert-danger';
                    alerta.textContent = data.message ?? 'Error al guardar el cliente.';
                }
                return;
            }

            // Agregar al select y seleccionarlo
            const select = document.getElementById('selectCliente');
            const option = new Option(
                data.cliente.nombre_completo + ' (' + data.cliente.numero_documento + ')',
                data.cliente.id,
                true,
                true
            );
            select.add(option);

            // Cerrar modal y limpiar
            bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente')).hide();

            // Limpiar campos del modal
            ['mc_nombre','mc_apellido','mc_numero_documento','mc_email','mc_telefono'].forEach(id => {
                document.getElementById(id).value = '';
            });
            document.getElementById('mc_tipo_documento').value = 'passport';
            document.getElementById('mc_pais_id').value = '';

        } catch (e) {
            const alerta = document.getElementById('modalClienteAlerta');
            alerta.className = 'alert alert-danger';
            alerta.textContent = 'Error de conexión. Intenta nuevamente.';
        } finally {
            spinner.classList.add('d-none');
            document.getElementById('btnGuardarCliente').disabled = false;
        }
    });

    // Limpiar errores al abrir el modal
    document.getElementById('modalNuevoCliente').addEventListener('show.bs.modal', clearErrors);
})();
</script>
