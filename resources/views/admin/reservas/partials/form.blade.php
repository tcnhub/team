<form action="{{ isset($reserva) ? route('admin.reservas.update', $reserva) : route('admin.reservas.store') }}"
      method="POST">

    @csrf
    @if(isset($reserva) && $reserva->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <!-- ── Código de Reserva (solo lectura) ── -->
        <div class="col-md-3">
            <label class="form-label">Código de Reserva</label>
            <input type="text" class="form-control bg-light"
                   value="{{ isset($reserva) ? $reserva->codigo_reserva : 'Se generará automáticamente' }}"
                   readonly>
        </div>

        <!-- ── TOUR ── -->
        <div class="col-md-9">
            <label class="form-label fw-semibold">Tour <span class="text-muted small">(opcional)</span></label>
            <select name="tour_id" id="selectTour"
                    class="form-select @error('tour_id') is-invalid @enderror">
                <option value="">— Sin tour asociado —</option>
                @foreach($tours ?? [] as $t)
                    <option value="{{ $t->id }}"
                            data-nombre="{{ $t->nombre_tour }}"
                            data-dias="{{ $t->duracion_dias }}"
                            data-precio="{{ $t->precio_base }}"
                            data-moneda="{{ $t->moneda }}"
                            {{ (isset($reserva) && $reserva->tour_id == $t->id) || old('tour_id') == $t->id ? 'selected' : '' }}>
                        {{ $t->codigo_tour }} — {{ $t->nombre_tour }}
                        @if($t->duracion_dias) ({{ $t->duracion_dias }}d) @endif
                        @if($t->precio_base) · {{ $t->moneda }} {{ number_format($t->precio_base, 2) }} @endif
                    </option>
                @endforeach
            </select>
            @error('tour_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <!-- Info del tour seleccionado -->
            <div id="tourInfo" class="mt-1 d-none">
                <span class="badge bg-info-subtle text-info border border-info-subtle" id="tourInfoBadge"></span>
            </div>
        </div>

        <!-- ── Cliente ── -->
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
                <i class="ri-information-line me-1"></i>Si el cliente no está en la lista, usa el botón <strong>+</strong>.
            </div>
        </div>

        <!-- ── Agente ── -->
        <div class="col-md-3">
            <label class="form-label">Agente</label>
            <select name="id_agente" class="form-select @error('id_agente') is-invalid @enderror">
                <option value="">Sin agente</option>
                @foreach($agentes ?? [] as $agente)
                    <option value="{{ $agente->id }}"
                        {{ (isset($reserva) && $reserva->id_agente == $agente->id) || old('id_agente') == $agente->id ? 'selected' : '' }}>
                        {{ $agente->nombres . ' ' . $agente->apellidos }}
                    </option>
                @endforeach
            </select>
            @error('id_agente')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- ── Fuente de reserva ── -->
        <div class="col-md-3">
            <label class="form-label">Fuente de Reserva</label>
            <select name="fuente_reserva" class="form-select">
                @foreach(['Oficina','Web','WhatsApp','Email','Teléfono','Referido','Otro'] as $f)
                    <option value="{{ $f }}"
                        {{ (isset($reserva) && $reserva->fuente_reserva == $f) || old('fuente_reserva') == $f ? 'selected' : '' }}>
                        {{ $f }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- ── Tipo de Reserva ── -->
        <div class="col-md-4">
            <label class="form-label">Tipo de Reserva <span class="text-danger">*</span></label>
            <input type="text" name="tipo_reserva" id="inputTipoReserva"
                   class="form-control @error('tipo_reserva') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->tipo_reserva : old('tipo_reserva') }}"
                   placeholder="Tour, Paquete, Hotel, Vuelo..." required>
            @error('tipo_reserva')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- ── Descripción del Servicio ── -->
        <div class="col-md-8">
            <label class="form-label">Descripción del Servicio</label>
            <input type="text" name="descripcion_servicio" id="inputDescripcion"
                   class="form-control @error('descripcion_servicio') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->descripcion_servicio : old('descripcion_servicio') }}"
                   placeholder="Machu Picchu 4D/3N - Abril 2026">
            @error('descripcion_servicio')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- ── Fechas ── -->
        <div class="col-md-3">
            <label class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
            <input type="text" name="fecha_inicio" id="inputFechaInicio"
                   data-date-format="Y-m-d"
                   class="form-control flatpickr-date @error('fecha_inicio') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->fecha_inicio->format('Y-m-d') : old('fecha_inicio') }}" required>
            @error('fecha_inicio')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">
                Fecha de Fin
                <span id="fechaFinHint" class="text-muted small d-none">(calculada por duración del tour)</span>
            </label>
            <input type="text" name="fecha_fin" id="inputFechaFin"
                   data-date-format="Y-m-d"
                   class="form-control flatpickr-date @error('fecha_fin') is-invalid @enderror"
                   value="{{ isset($reserva) && $reserva->fecha_fin ? $reserva->fecha_fin->format('Y-m-d') : old('fecha_fin') }}">
            @error('fecha_fin')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- ── Disponibilidad específica (fecha del tour) ── -->
        <div class="col-md-6">
            <label class="form-label">
                Fecha de Disponibilidad Vinculada
                <span class="text-muted small">(opcional)</span>
            </label>
            <select name="availability_id" id="selectAvailability"
                    class="form-select @error('availability_id') is-invalid @enderror">
                <option value="">— No vincular con día específico —</option>
                @if(isset($reserva) && $reserva->availability_id && $reserva->availability)
                    <option value="{{ $reserva->availability->id }}" selected>
                        {{ $reserva->availability->fecha->format('d/m/Y') }}
                        ({{ $reserva->availability->espacios_disponibles }} disponibles)
                    </option>
                @endif
            </select>
            <div class="form-text" id="availabilityHint">
                Selecciona un tour primero para cargar fechas disponibles.
            </div>
            @error('availability_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- ── Pasajeros ── -->
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
        </div>

        <div class="col-md-3">
            <label class="form-label">Niños</label>
            <input type="number" name="num_ninos" min="0"
                   class="form-control @error('num_ninos') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->num_ninos : old('num_ninos', 0) }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Bebés</label>
            <input type="number" name="num_bebes" min="0"
                   class="form-control @error('num_bebes') is-invalid @enderror"
                   value="{{ isset($reserva) ? $reserva->num_bebes : old('num_bebes', 0) }}">
        </div>

        <!-- ── Información Financiera ── -->
        <input type="hidden" name="moneda" id="inputMoneda" value="USD">

        <div class="col-md-4">
            <label class="form-label">Precio Total <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="precio_total" id="inputPrecioTotal"
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
        </div>

        <!-- ── Notas ── -->
        <div class="col-12">
            <label class="form-label">Notas / Observaciones</label>
            <textarea name="notas" class="form-control" rows="3">{{ isset($reserva) ? $reserva->notas : old('notas') }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Requisitos Especiales</label>
            <textarea name="requisitos_especiales" class="form-control" rows="2">{{ isset($reserva) ? $reserva->requisitos_especiales : old('requisitos_especiales') }}</textarea>
        </div>

        <!-- ── Estado (solo en edición) ── -->
        @if(isset($reserva) && $reserva->exists)
            <div class="col-md-4">
                <label class="form-label">Estado de la Reserva</label>
                <select name="estado_reserva" class="form-select @error('estado_reserva') is-invalid @enderror">
                    @foreach(['pendiente' => 'Pendiente', 'confirmada' => 'Confirmada', 'pagada' => 'Pagada', 'cancelada' => 'Cancelada', 'reembolsada' => 'Reembolsada', 'completada' => 'Completada'] as $val => $label)
                        <option value="{{ $val }}" {{ $reserva->estado_reserva == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('estado_reserva')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        @endif

    {{-- ── Pago Inicial (solo en creación) ── --}}
    @unless(isset($reserva) && $reserva->exists)
        <div class="col-12 mt-2">
            <hr class="border-dashed">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="ri-bank-card-line fs-5 text-success"></i>
                <h6 class="mb-0 fw-semibold">Pago Inicial <span class="text-muted fw-normal small">(opcional)</span></h6>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Monto Inicial</label>
                    <input type="number" step="0.01" name="pago_inicial_monto" id="inputPagoInicial"
                           class="form-control" placeholder="0.00" min="0">
                    <div class="form-text" id="pagoInicialHint"></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Método de Pago</label>
                    <select name="pago_inicial_metodo" class="form-select">
                        <option value="">— Seleccionar —</option>
                        @foreach(\App\Models\Pago::metodosLabel() as $val => $label)
                            <option value="{{ $val }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">N° Operación / Código</label>
                    <input type="text" name="pago_inicial_operacion" class="form-control"
                           placeholder="Código transferencia, Yape...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="pago_inicial_tipo" class="form-select">
                        @foreach(\App\Models\Pago::tiposLabel() as $val => $label)
                            <option value="{{ $val }}" {{ $val === 'inicial' ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endunless

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
                <h5 class="modal-title"><i class="ri-user-add-line me-2"></i>Registrar Nuevo Cliente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalClienteAlerta" class="d-none"></div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" id="mc_nombre" class="form-control" placeholder="Nombres">
                        <div class="invalid-feedback" id="err_nombre"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Apellido <span class="text-danger">*</span></label>
                        <input type="text" id="mc_apellido" class="form-control" placeholder="Apellidos">
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
                        <input type="text" id="mc_numero_documento" class="form-control" placeholder="Número de documento">
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
                        <input type="email" id="mc_email" class="form-control" placeholder="correo@ejemplo.com">
                        <div class="invalid-feedback" id="err_email"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Teléfono / WhatsApp</label>
                        <input type="text" id="mc_telefono" class="form-control" placeholder="+51 999 999 999">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    // ── Variables globales del formulario ──────────────────────────────────
    const AVAIL_BASE_URL = '{{ rtrim(url("admin/tours"), "/") }}';

    // Duración del tour seleccionado (días), null si no hay tour
    let tourDias = null;

    // ── Tour selector: auto-rellenar campos ───────────────────────────────
    const selectTour   = document.getElementById('selectTour');
    const inputTipo    = document.getElementById('inputTipoReserva');
    const inputDesc    = document.getElementById('inputDescripcion');
    const inputFechaI  = document.getElementById('inputFechaInicio');
    const inputFechaF  = document.getElementById('inputFechaFin');
    const inputPrecio  = document.getElementById('inputPrecioTotal');
    const inputMoneda  = document.getElementById('inputMoneda');
    const selAvail     = document.getElementById('selectAvailability');
    const tourInfo     = document.getElementById('tourInfo');
    const tourInfoBadge= document.getElementById('tourInfoBadge');
    const fechaFinHint = document.getElementById('fechaFinHint');
    const availHint    = document.getElementById('availabilityHint');

    function calcFechaFin() {
        if (!tourDias || !inputFechaI.value) return;
        const inicio = new Date(inputFechaI.value + 'T00:00:00');
        inicio.setDate(inicio.getDate() + tourDias - 1);
        const y = inicio.getFullYear();
        const m = String(inicio.getMonth() + 1).padStart(2, '0');
        const d = String(inicio.getDate()).padStart(2, '0');
        const fechaFin = `${y}-${m}-${d}`;
        if (window.setFlatpickrDate) {
            window.setFlatpickrDate(inputFechaF, fechaFin);
        } else {
            inputFechaF.value = fechaFin;
        }
    }

    async function cargarAvailabilities(tourId) {
        selAvail.innerHTML = '<option value="">Cargando fechas...</option>';
        try {
            const res  = await fetch(`${AVAIL_BASE_URL}/${tourId}/availabilities-json`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            selAvail.innerHTML = '<option value="">— No vincular con día específico —</option>';
            if (data.length === 0) {
                availHint.textContent = 'No hay fechas disponibles generadas para este tour.';
            } else {
                availHint.textContent = `${data.length} fechas disponibles cargadas.`;
                data.forEach(a => {
                    const opt = new Option(
                        `${a.fecha_display} (${a.espacios_disponibles} disponibles)`,
                        a.id
                    );
                    selAvail.add(opt);
                });
            }
        } catch (e) {
            selAvail.innerHTML = '<option value="">— Error al cargar fechas —</option>';
            availHint.textContent = 'No se pudieron cargar las fechas disponibles.';
        }
    }

    selectTour.addEventListener('change', function () {
        const opt = this.selectedOptions[0];
        if (!this.value) {
            tourDias = null;
            tourInfo.classList.add('d-none');
            fechaFinHint.classList.add('d-none');
            selAvail.innerHTML = '<option value="">— No vincular con día específico —</option>';
            availHint.textContent = 'Selecciona un tour primero para cargar fechas disponibles.';
            return;
        }

        const nombre = opt.dataset.nombre ?? '';
        tourDias     = parseInt(opt.dataset.dias) || null;
        const precio = opt.dataset.precio;
        // Auto-rellenar tipo y descripción si están vacíos
        if (!inputTipo.value) inputTipo.value = 'Tour';
        if (!inputDesc.value) inputDesc.value = nombre + (tourDias ? ` ${tourDias}D/${Math.max(tourDias-1,1)}N` : '');

        // Auto-rellenar precio si está vacío
        if (precio && !inputPrecio.value) {
            inputPrecio.value = parseFloat(precio).toFixed(2);
        }

        // Mostrar badge info
        tourInfoBadge.textContent = nombre + (tourDias ? ` · ${tourDias} día(s)` : '');
        tourInfo.classList.remove('d-none');

        // Calcular fecha fin si hay fecha inicio
        if (tourDias) {
            fechaFinHint.classList.remove('d-none');
            calcFechaFin();
        }

        // Cargar disponibilidades del tour vía AJAX
        cargarAvailabilities(this.value);
    });

    // Recalcular fecha_fin cuando cambia fecha_inicio
    inputFechaI.addEventListener('change', calcFechaFin);

    // Disparar evento change si ya hay un tour seleccionado (modo edición / old())
    if (selectTour.value) {
        selectTour.dispatchEvent(new Event('change'));
    }

    // Hint de pago inicial: mostrar saldo sugerido al escribir precio
    const inputPagoInicial = document.getElementById('inputPagoInicial');
    const pagoHint = document.getElementById('pagoInicialHint');
    if (inputPrecio && inputPagoInicial && pagoHint) {
        inputPrecio.addEventListener('input', function() {
            const precio = parseFloat(this.value) || 0;
            if (precio > 0) pagoHint.textContent = `Total a pagar: ${precio.toFixed(2)}`;
        });
    }

    // ── Modal: Crear Nuevo Cliente ─────────────────────────────────────────
    const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';
    const STORE_URL = '{{ route("admin.clientes.store-quick") }}';
    const mcFields  = ['nombre', 'apellido', 'tipo_documento', 'numero_documento', 'pais_id', 'email', 'telefono'];

    function clearErrors() {
        mcFields.forEach(f => {
            const el = document.getElementById('mc_' + f);
            if (el) el.classList.remove('is-invalid');
            const err = document.getElementById('err_' + f);
            if (err) err.textContent = '';
        });
        document.getElementById('modalClienteAlerta').className = 'd-none';
    }

    function showFieldError(field, msg) {
        const el = document.getElementById('mc_' + field);
        if (el) el.classList.add('is-invalid');
        const err = document.getElementById('err_' + field);
        if (err) err.textContent = msg;
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
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body   : JSON.stringify(payload),
            });
            const data = await res.json();

            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    Object.entries(data.errors).forEach(([f, msgs]) => showFieldError(f, msgs[0]));
                } else {
                    const alerta = document.getElementById('modalClienteAlerta');
                    alerta.className = 'alert alert-danger';
                    alerta.textContent = data.message ?? 'Error al guardar el cliente.';
                }
                return;
            }

            const select = document.getElementById('selectCliente');
            select.add(new Option(
                data.cliente.nombre_completo + ' (' + data.cliente.numero_documento + ')',
                data.cliente.id, true, true
            ));

            bootstrap.Modal.getInstance(document.getElementById('modalNuevoCliente')).hide();
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

    document.getElementById('modalNuevoCliente').addEventListener('show.bs.modal', clearErrors);
})();
</script>
