@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Reservas — ' . $tour->nombre_tour]); ?>
    @include('layouts.head-css')
    <style>
        .res-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 3px; }
        .res-header-day {
            text-align: center; font-size: .70rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .04em;
            color: #6c757d; padding: 3px 0 5px;
        }
        .res-cell {
            min-height: 88px; border: 1px solid #e9ecef; border-radius: 6px;
            padding: 4px 5px; background: #fff; font-size: .72rem;
            cursor: pointer; transition: background .12s;
        }
        .res-cell:hover:not(.empty) { background: #f0f7ff; border-color: #90c4ff; }
        .res-cell.empty  { background: transparent; border-color: transparent; cursor: default; }
        .res-cell.today  { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,.18); }
        .res-day-num { font-size: .75rem; font-weight: 700; color: #495057; line-height: 1; margin-bottom: 3px; }
        .res-cell.today .res-day-num {
            background: #0d6efd; color: #fff; width: 20px; height: 20px;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }
        .res-badge {
            display: block; border-radius: 4px; padding: 1px 4px; margin-bottom: 2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            font-size: .68rem; cursor: pointer; line-height: 1.4;
        }
        .res-badge.inicio { background:#d1e7dd; color:#0f5132; border-left:3px solid #198754; }
        .res-badge.medio  { background:#cfe2ff; color:#084298; border-left:3px solid #0d6efd; }
        .res-badge.fin    { background:#f8d7da; color:#842029; border-left:3px solid #dc3545; }
        .res-badge.unico  { background:#fff3cd; color:#664d03; border-left:3px solid #ffc107; }
        .add-btn {
            display: block; width: 100%; border: none; background: none;
            color: #adb5bd; font-size: .65rem; text-align: center;
            padding: 1px 0; border-radius: 3px; line-height: 1.3; margin-top: auto;
        }
        .res-cell:hover .add-btn { color: #0d6efd; background: #e7f1ff; }
        .month-card { margin-bottom: 2rem; }
        .month-title {
            font-size: 1rem; font-weight: 700; padding: .5rem 1rem;
            background: #f8f9fa; border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
    </style>
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Breadcrumb --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">Reservas: {{ $tour->nombre_tour }}</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.tours.index') }}">Tours</a></li>
                                    <li class="breadcrumb-item active">Reservas Calendario</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Alerts --}}
                <div id="calendarAlerts"></div>

                {{-- Header --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body py-2 d-flex flex-wrap align-items-center gap-3">
                                <div>
                                    <span class="fw-semibold">Tour:</span> {{ $tour->nombre_tour }}
                                    @if($tour->duracion_dias)
                                        <span class="ms-2 badge bg-info">{{ $tour->duracion_dias }} días</span>
                                    @endif
                                    @if($tour->codigo_tour)
                                        <span class="ms-1 badge bg-secondary">{{ $tour->codigo_tour }}</span>
                                    @endif
                                </div>
                                <form method="GET" class="d-flex align-items-center gap-2 ms-auto">
                                    <label class="fw-semibold mb-0">Año:</label>
                                    <select name="anio" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
                                        @foreach($aniosDisponibles as $a)
                                            <option value="{{ $a }}" {{ $a == $anio ? 'selected' : '' }}>{{ $a }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <a href="{{ route('admin.tours.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ri-arrow-left-line"></i> Volver a Tours
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Leyenda --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="res-badge inicio d-inline-block" style="width:auto;cursor:default">Día inicio</span>
                            <span class="res-badge medio d-inline-block" style="width:auto;cursor:default">Días intermedios</span>
                            <span class="res-badge fin d-inline-block" style="width:auto;cursor:default">Día fin</span>
                            <span class="res-badge unico d-inline-block" style="width:auto;cursor:default">Tour de 1 día</span>
                            <span class="text-muted small ms-auto">
                                <strong id="totalReservasCount">{{ $reservas->count() }}</strong> reservas en {{ $anio }}
                                <span class="text-primary ms-2"><i class="ri-cursor-line"></i> Click en un día para agregar reserva</span>
                            </span>
                        </div>
                    </div>
                </div>

                @php
                    $meses = [
                        1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',
                        5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',
                        9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre',
                    ];
                    $hoy = \Carbon\Carbon::today();
                @endphp

                @foreach($meses as $numMes => $nombreMes)
                    @php
                        $primerDia    = \Carbon\Carbon::create($anio, $numMes, 1);
                        $ultimoDia    = $primerDia->copy()->endOfMonth();
                        $offsetInicio = ($primerDia->dayOfWeek + 6) % 7;
                    @endphp
                    <div class="card month-card">
                        <div class="month-title">{{ $nombreMes }} {{ $anio }}</div>
                        <div class="card-body p-2">
                            <div class="res-grid">
                                @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d)
                                    <div class="res-header-day">{{ $d }}</div>
                                @endforeach

                                @for($i = 0; $i < $offsetInicio; $i++)
                                    <div class="res-cell empty"></div>
                                @endfor

                                @for($dia = 1; $dia <= $ultimoDia->day; $dia++)
                                    @php
                                        $fechaActual = \Carbon\Carbon::create($anio, $numMes, $dia);
                                        $fechaKey    = $fechaActual->format('Y-m-d');
                                        $esHoy       = $fechaActual->isSameDay($hoy);
                                        $reservasDia = $mapaFechas[$fechaKey] ?? [];
                                    @endphp
                                    <div class="res-cell {{ $esHoy ? 'today' : '' }}"
                                         data-fecha="{{ $fechaKey }}"
                                         id="cell-{{ $fechaKey }}">
                                        <div class="res-day-num">{{ $dia }}</div>
                                        <div class="badges-container" id="badges-{{ $fechaKey }}">
                                        @foreach($reservasDia as $res)
                                            @php
                                                $esInicio = $res->fecha_inicio->isSameDay($fechaActual);
                                                $esFin    = $res->fecha_fin_calculada->isSameDay($fechaActual);
                                                $esUnico  = $esInicio && $esFin;
                                                $tipo     = $esUnico ? 'unico' : ($esInicio ? 'inicio' : ($esFin ? 'fin' : 'medio'));
                                                $nombre   = $res->cliente?->nombre_completo ?? $res->codigo_reserva;
                                            @endphp
                                            <span class="res-badge {{ $tipo }}"
                                                  title="{{ $res->codigo_reserva }} — {{ $nombre }}&#10;{{ $res->fecha_inicio->format('d/m') }} → {{ $res->fecha_fin_calculada->format('d/m') }}&#10;Estado: {{ $res->estado_texto }}"
                                                  data-bs-toggle="tooltip"
                                                  onclick="event.stopPropagation(); window.location='{{ route('admin.reservas.show', $res) }}'">
                                                @if($esInicio || $esUnico)<i class="ri-user-line"></i>@endif
                                                {{ \Illuminate\Support\Str::limit($nombre, 12) }}
                                            </span>
                                        @endforeach
                                        </div>
                                        <button class="add-btn" onclick="event.stopPropagation(); abrirModalReserva('{{ $fechaKey }}')">
                                            <i class="ri-add-line"></i> agregar
                                        </button>
                                    </div>
                                @endfor

                                @php
                                    $totalCeldas = $offsetInicio + $ultimoDia->day;
                                    $resto  = $totalCeldas % 7;
                                    $vacias = $resto > 0 ? 7 - $resto : 0;
                                @endphp
                                @for($i = 0; $i < $vacias; $i++)
                                    <div class="res-cell empty"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>

{{-- ══════════════════ MODAL: Nueva Reserva ══════════════════ --}}
<div class="modal fade" id="modalNuevaReserva" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="ri-calendar-check-line me-2"></i>
                    Nueva Reserva — <span id="modalFechaLabel" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalReservaAlerta" class="d-none mb-3"></div>

                <div class="row g-3">

                    {{-- Cliente --}}
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select id="mr_cliente" class="form-select">
                                <option value="">Seleccionar cliente...</option>
                                @foreach($clientes as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->nombre_completo }}
                                        ({{ $c->numero_documento ?? '—' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="invalid-feedback" id="err_mr_cliente"></div>
                    </div>

                    {{-- Agente --}}
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Agente</label>
                        <select id="mr_agente" class="form-select">
                            <option value="">Sin agente</option>
                            @foreach($agentes as $a)
                                <option value="{{ $a->id }}">{{ $a->nombres }} {{ $a->apellidos }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Fecha inicio (pre-llenada) --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Inicio <span class="text-danger">*</span></label>
                                        <input type="text" id="mr_fecha_inicio" class="form-control flatpickr-date" data-date-format="Y-m-d" required>
                        <div class="invalid-feedback" id="err_mr_fecha_inicio"></div>
                    </div>

                    {{-- Fecha fin (calculada) --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            Fecha Fin
                            <small class="text-muted">(por duración)</small>
                        </label>
                                        <input type="text" id="mr_fecha_fin" class="form-control flatpickr-date" data-date-format="Y-m-d">
                    </div>

                    {{-- Pasajeros --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Pasajeros</label>
                        <input type="number" id="mr_num_pasajeros" class="form-control" value="1" min="1">
                        <div class="invalid-feedback" id="err_mr_num_pasajeros"></div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Adultos</label>
                        <input type="number" id="mr_num_adultos" class="form-control" value="1" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Niños</label>
                        <input type="number" id="mr_num_ninos" class="form-control" value="0" min="0">
                    </div>

                    {{-- Precio --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Precio Total <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" id="mr_precio_total" class="form-control" placeholder="0.00" min="0">
                        <div class="invalid-feedback" id="err_mr_precio_total"></div>
                    </div>
                    <input type="hidden" id="mr_moneda" value="USD">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Descuento</label>
                        <input type="number" step="0.01" id="mr_descuento" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Fuente</label>
                        <select id="mr_fuente" class="form-select">
                            @foreach(['Oficina','Web','WhatsApp','Email','Teléfono','Referido','Otro'] as $f)
                                <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Notas --}}
                    <div class="col-12">
                        <label class="form-label">Notas</label>
                        <textarea id="mr_notas" class="form-control" rows="2" placeholder="Observaciones..."></textarea>
                    </div>

                    {{-- ── Pago Inicial ── --}}
                    <div class="col-12">
                        <hr class="border-dashed my-1">
                        <p class="fw-semibold mb-2 text-success"><i class="ri-bank-card-line me-1"></i>Pago Inicial <small class="text-muted fw-normal">(opcional)</small></p>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Monto Inicial</label>
                        <input type="number" step="0.01" id="mr_pago_monto" class="form-control" placeholder="0.00" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Método de Pago</label>
                        <select id="mr_pago_metodo" class="form-select">
                            <option value="">— Seleccionar —</option>
                            @foreach(\App\Models\Pago::metodosLabel() as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Código / N° Operación</label>
                        <input type="text" id="mr_pago_operacion" class="form-control" placeholder="N° transferencia, código Yape...">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarReserva">
                    <span id="btnReservaSpinner" class="spinner-border spinner-border-sm me-1 d-none"></span>
                    <i class="ri-save-line me-1"></i>Guardar Reserva
                </button>
            </div>
        </div>
    </div>
</div>

@include('layouts.customizer')
@include('layouts.vendor-scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
// ── Constantes ──────────────────────────────────────────────────────────────
const TOUR_ID       = {{ $tour->id }};
const TOUR_DIAS     = {{ $tour->duracion_dias ?? 'null' }};
const TOUR_PRECIO   = {{ $tour->precio_base ?? 'null' }};
const TOUR_MONEDA   = 'USD';
const STORE_URL     = '{{ route('admin.tours.reservas.store-ajax', $tour) }}';
const CSRF          = document.querySelector('meta[name="csrf-token"]')?.content;

// ── Activar tooltips ─────────────────────────────────────────────────────────
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
    new bootstrap.Tooltip(el, { html: true });
});

// ── Click en celda ───────────────────────────────────────────────────────────
document.querySelectorAll('.res-cell:not(.empty)').forEach(cell => {
    cell.addEventListener('click', function () {
        abrirModalReserva(this.dataset.fecha);
    });
});

function abrirModalReserva(fecha) {
    limpiarErroresModal();
    if (window.setFlatpickrDate) {
        window.setFlatpickrDate(document.getElementById('mr_fecha_inicio'), fecha);
    } else {
        document.getElementById('mr_fecha_inicio').value = fecha;
    }
    document.getElementById('modalFechaLabel').textContent = formatFecha(fecha);

    // Calcular fecha_fin automáticamente
    if (TOUR_DIAS) {
        const d = new Date(fecha + 'T00:00:00');
        d.setDate(d.getDate() + TOUR_DIAS - 1);
        const fechaFin = d.toISOString().slice(0, 10);
        if (window.setFlatpickrDate) {
            window.setFlatpickrDate(document.getElementById('mr_fecha_fin'), fechaFin);
        } else {
            document.getElementById('mr_fecha_fin').value = fechaFin;
        }
    } else {
        if (window.setFlatpickrDate) {
            window.setFlatpickrDate(document.getElementById('mr_fecha_fin'), '');
        } else {
            document.getElementById('mr_fecha_fin').value = '';
        }
    }

    // Prellenar precio del tour si hay uno
    if (TOUR_PRECIO) {
        document.getElementById('mr_precio_total').value = parseFloat(TOUR_PRECIO).toFixed(2);
    }
    if (TOUR_MONEDA) {
        document.getElementById('mr_moneda').value = TOUR_MONEDA;
    }

    new bootstrap.Modal(document.getElementById('modalNuevaReserva')).show();
}

function formatFecha(iso) {
    const [y, m, d] = iso.split('-');
    const meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    return `${parseInt(d)} ${meses[parseInt(m)-1]} ${y}`;
}

// ── Guardar reserva vía AJAX ─────────────────────────────────────────────────
document.getElementById('btnGuardarReserva').addEventListener('click', async function () {
    limpiarErroresModal();

    const spinner = document.getElementById('btnReservaSpinner');
    spinner.classList.remove('d-none');
    this.disabled = true;

    const payload = {
        id_cliente           : document.getElementById('mr_cliente').value,
        id_agente            : document.getElementById('mr_agente').value || null,
        fecha_inicio         : document.getElementById('mr_fecha_inicio').value,
        fecha_fin            : document.getElementById('mr_fecha_fin').value || null,
        num_pasajeros        : document.getElementById('mr_num_pasajeros').value,
        num_adultos          : document.getElementById('mr_num_adultos').value,
        num_ninos            : document.getElementById('mr_num_ninos').value,
        num_bebes            : 0,
        moneda               : document.getElementById('mr_moneda').value,
        precio_total         : document.getElementById('mr_precio_total').value,
        descuento            : document.getElementById('mr_descuento').value || 0,
        notas                : document.getElementById('mr_notas').value || null,
        fuente_reserva       : document.getElementById('mr_fuente').value,
        pago_inicial_monto   : document.getElementById('mr_pago_monto').value || null,
        pago_inicial_metodo  : document.getElementById('mr_pago_metodo').value || null,
        pago_inicial_operacion: document.getElementById('mr_pago_operacion').value || null,
    };

    try {
        const res  = await fetch(STORE_URL, {
            method : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN' : CSRF,
                'Accept'       : 'application/json',
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json();

        if (!res.ok) {
            if (res.status === 422 && data.errors) {
                const fieldMap = {
                    id_cliente   : 'mr_cliente',
                    fecha_inicio : 'mr_fecha_inicio',
                    num_pasajeros: 'mr_num_pasajeros',
                    precio_total : 'mr_precio_total',
                };
                Object.entries(data.errors).forEach(([f, msgs]) => {
                    const elId = fieldMap[f] ?? null;
                    if (elId) {
                        const el = document.getElementById(elId);
                        if (el) el.classList.add('is-invalid');
                        const err = document.getElementById('err_' + elId);
                        if (err) err.textContent = msgs[0];
                    }
                });
                mostrarAlertaModal('danger', data.message ?? 'Corrige los errores del formulario.');
            } else {
                mostrarAlertaModal('danger', data.message ?? 'Error al guardar la reserva.');
            }
            return;
        }

        // ── Éxito: añadir badges al calendario ──────────────────────────
        agregarBadgesCalendario(data.reserva);
        bootstrap.Modal.getInstance(document.getElementById('modalNuevaReserva')).hide();
        limpiarFormularioModal();
        mostrarAlertaExito(data.reserva);

    } catch (e) {
        mostrarAlertaModal('danger', 'Error de conexión. Intenta nuevamente.');
    } finally {
        spinner.classList.add('d-none');
        document.getElementById('btnGuardarReserva').disabled = false;
    }
});

// ── Añadir badges en las celdas del calendario ───────────────────────────────
function agregarBadgesCalendario(reserva) {
    const inicio  = new Date(reserva.fecha_inicio + 'T00:00:00');
    const fin     = new Date(reserva.fecha_fin     + 'T00:00:00');
    const nombre  = reserva.cliente_nombre ?? reserva.codigo_reserva;
    const url     = reserva.show_url;

    let cursor = new Date(inicio);
    while (cursor <= fin) {
        const key  = cursor.toISOString().slice(0, 10);
        const cont = document.getElementById('badges-' + key);
        if (cont) {
            const esInicio = key === reserva.fecha_inicio;
            const esFin    = key === reserva.fecha_fin;
            const esUnico  = esInicio && esFin;
            const tipo     = esUnico ? 'unico' : (esInicio ? 'inicio' : (esFin ? 'fin' : 'medio'));

            const badge = document.createElement('span');
            badge.className = `res-badge ${tipo}`;
            badge.title     = `${reserva.codigo_reserva} — ${nombre}`;
            badge.innerHTML = (esInicio || esUnico ? '<i class="ri-user-line"></i> ' : '') +
                              nombre.substring(0, 12);
            badge.onclick   = (e) => { e.stopPropagation(); window.location = url; };
            cont.appendChild(badge);
        }
        cursor.setDate(cursor.getDate() + 1);
    }

    // Actualizar contador
    const cnt = document.getElementById('totalReservasCount');
    if (cnt) cnt.textContent = parseInt(cnt.textContent) + 1;
}

function mostrarAlertaExito(reserva) {
    const box = document.getElementById('calendarAlerts');
    box.innerHTML = `
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            Reserva <strong>${reserva.codigo_reserva}</strong> creada para el ${formatFecha(reserva.fecha_inicio)}.
            <a href="${reserva.show_url}" class="alert-link ms-2">Ver detalle →</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
    box.scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => { box.innerHTML = ''; }, 7000);
}

function mostrarAlertaModal(tipo, msg) {
    const el = document.getElementById('modalReservaAlerta');
    el.className = `alert alert-${tipo}`;
    el.textContent = msg;
}

function limpiarErroresModal() {
    document.getElementById('modalReservaAlerta').className = 'd-none';
    ['mr_cliente','mr_fecha_inicio','mr_num_pasajeros','mr_precio_total'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.remove('is-invalid');
        const err = document.getElementById('err_' + id);
        if (err) err.textContent = '';
    });
}

function limpiarFormularioModal() {
    ['mr_cliente','mr_agente','mr_fecha_inicio','mr_fecha_fin',
     'mr_num_pasajeros','mr_num_adultos','mr_num_ninos',
     'mr_precio_total','mr_descuento','mr_notas',
     'mr_pago_monto','mr_pago_metodo','mr_pago_operacion'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else {
            const value = (id === 'mr_num_pasajeros' || id === 'mr_num_adultos') ? '1'
                : (id === 'mr_num_ninos' || id === 'mr_descuento') ? '0' : '';
            if (window.setFlatpickrDate && (id === 'mr_fecha_inicio' || id === 'mr_fecha_fin')) {
                window.setFlatpickrDate(el, value);
            } else {
                el.value = value;
            }
        }
    });
}
</script>
</body>
</html>
