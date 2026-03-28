@include('layouts.main')

<head>

    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Lista de Tours')); ?>

        <!-- jsvectormap css -->
    <link href="{{ asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Swiper slider css -->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    @include('layouts.head-css')

</head>

<body>

<!-- Begin page -->
<div id="layout-wrapper">

    @include('layouts.menu')

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Lista de Tours</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.tours.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-middle"></i> Nuevo Tour
                                    </a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <!-- ==================== FILTROS DE BÚSQUEDA ==================== -->
                                <form method="GET" action="{{ route('admin.tours.index') }}" class="mb-4" data-auto-filter="true">
                                    <div class="row g-3">

                                        <!-- Código -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Código</label>
                                            <input type="text" name="codigo_tour" class="form-control"
                                                   value="{{ request('codigo_tour') }}" placeholder="MP-001">
                                        </div>

                                        <!-- Nombre -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Nombre del Tour</label>
                                            <input type="text" name="nombre_tour" class="form-control"
                                                   value="{{ request('nombre_tour') }}" placeholder="Machu Picchu">
                                        </div>

                                        <!-- Duración -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Duración (días)</label>
                                            <input type="number" name="duracion_dias" class="form-control"
                                                   value="{{ request('duracion_dias') }}" placeholder="2" min="1">
                                        </div>

                                        <!-- Precio Base + Orden -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Precio Base</label>
                                            <div class="input-group">
                                                <input type="number" name="precio_base" class="form-control"
                                                       value="{{ request('precio_base') }}" placeholder="Precio mínimo">
                                                <select name="precio_order" class="form-select" style="max-width: 160px;">
                                                    <option value="">Ordenar por precio</option>
                                                    <option value="asc" {{ request('precio_order') === 'asc' ? 'selected' : '' }}>Menor a Mayor</option>
                                                    <option value="desc" {{ request('precio_order') === 'desc' ? 'selected' : '' }}>Mayor a Menor</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Dificultad -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Dificultad</label>
                                            <select name="nivel_dificultad" class="form-select">
                                                <option value="">Todas</option>
                                                <option value="Fácil" {{ request('nivel_dificultad') === 'Fácil' ? 'selected' : '' }}>Fácil</option>
                                                <option value="Moderado" {{ request('nivel_dificultad') === 'Moderado' ? 'selected' : '' }}>Moderado</option>
                                                <option value="Difícil" {{ request('nivel_dificultad') === 'Difícil' ? 'selected' : '' }}>Difícil</option>
                                                <option value="Extremo" {{ request('nivel_dificultad') === 'Extremo' ? 'selected' : '' }}>Extremo</option>
                                            </select>
                                        </div>

                                        <!-- Estado -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Estado</label>
                                            <select name="estado" class="form-select">
                                                <option value="">Todos</option>
                                                <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-search-line"></i> Buscar
                                            </button>
                                            <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary ms-2">
                                                <i class="ri-refresh-line"></i> Limpiar Filtros
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                <!-- ======================================================== -->

                                <div class="live-preview">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Código</th>
                                                <th scope="col">Nombre del Tour</th>
                                                <th scope="col">Duración</th>
                                                <th scope="col">Precio Base</th>
                                                <th scope="col">Dificultad</th>
                                                <th scope="col">Estado</th>
                                                <th scope="col" width="160">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse ($tours as $tour)
                                                <tr>
                                                    <td>{{ $tour->id }}</td>
                                                    <td><a href="#" class="fw-semibold">{{ $tour->codigo_tour }}</a></td>
                                                    <td>{{ $tour->nombre_tour }}</td>
                                                    <td>
                                                        @if($tour->duracion_dias)
                                                            {{ $tour->duracion_dias }} días
                                                            @if($tour->duracion_noches)
                                                                / {{ $tour->duracion_noches }} noches
                                                            @endif
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($tour->precio_base)
                                                            <strong>$ {{ number_format($tour->precio_base, 2) }}</strong>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($tour->nivel_dificultad)
                                                            <span class="badge bg-info">{{ $tour->nivel_dificultad }}</span>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="{{ $tour->estado ? 'text-success' : 'text-danger' }}">
                                                            <i class="ri-checkbox-circle-line fs-17 align-middle"></i>
                                                            {{ $tour->estado ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-sm btn-soft-info" title="Ver detalle">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-soft-primary btn-nueva-reserva"
                                                                    title="Nueva Reserva"
                                                                    data-tour-id="{{ $tour->id }}"
                                                                    data-tour-nombre="{{ $tour->nombre_tour }}"
                                                                    data-tour-precio="{{ $tour->precio_base ?? '' }}"
                                                                    data-tour-dias="{{ $tour->duracion_dias ?? '' }}"
                                                                    data-calendario-url="{{ route('admin.tours.reservas.calendario', $tour) }}"
                                                                    data-ajax-url="{{ route('admin.tours.reservas.store-ajax', $tour) }}"
                                                                    data-addons-url="{{ route('admin.tours.addons.json', $tour) }}">
                                                                <i class="ri-calendar-check-line"></i>
                                                            </button>
                                                            <a href="{{ route('admin.tours.reservas.calendario', $tour) }}" class="btn btn-sm btn-soft-success" title="Ver reservas en calendario">
                                                                <i class="ri-calendar-2-line"></i>
                                                            </a>
                                                            <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-sm btn-soft-warning" title="Editar">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                            <form action="{{ route('admin.tours.destroy', $tour) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-soft-danger delete-btn"
                                                                        title="Eliminar" data-name="{{ $tour->nombre_tour }}">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="9" class="text-center py-4">
                                                        <i class="ri-folder-3-line display-4 text-muted"></i>
                                                        <p class="mt-3 mb-0">No hay tours registrados</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginator Bootstrap 5 -->
                                    @if($tours->hasPages())
                                        <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                                            <div class="text-muted small">
                                                Mostrando <strong>{{ $tours->firstItem() ?? 0 }}</strong> al
                                                <strong>{{ $tours->lastItem() ?? 0 }}</strong> de
                                                <strong>{{ $tours->total() }}</strong> tours
                                            </div>
                                            <nav aria-label="Page navigation">
                                                {{ $tours->links('pagination::bootstrap-5') }}
                                            </nav>
                                        </div>
                                    @endif

                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>

            </div>
        </div>

        @include('layouts.footer')
    </div>
</div>

{{-- ── Modal: Nueva Reserva desde Tours Index ───────────────────────────────── --}}
<div class="modal fade" id="modalNuevaReservaTour" tabindex="-1" aria-labelledby="modalNRTLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNRTLabel">
                    <i class="ri-calendar-check-line me-1"></i>
                    Nueva Reserva — <span id="nrtTourNombre"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="nrtAlertError" class="alert alert-danger d-none"></div>
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="row g-3">
                    {{-- Cliente --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Cliente <span class="text-danger">*</span></label>
                        <select id="nrt_cliente" class="form-select">
                            <option value="">— Seleccionar cliente —</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre_completo }} ({{ $c->numero_documento }})</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="err_nrt_cliente"></div>
                    </div>
                    {{-- Agente --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Agente</label>
                        <select id="nrt_agente" class="form-select">
                            <option value="">— Sin agente —</option>
                            @foreach($agentes as $a)
                                <option value="{{ $a->id }}">{{ $a->nombre_completo }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Fecha Inicio --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="text" id="nrt_fecha_inicio" class="form-control flatpickr-date" data-date-format="Y-m-d">
                        <div class="invalid-feedback" id="err_nrt_fecha_inicio"></div>
                    </div>
                    {{-- Fecha Fin --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Fecha Fin</label>
                        <input type="text" id="nrt_fecha_fin" class="form-control flatpickr-date" data-date-format="Y-m-d">
                    </div>
                    <input type="hidden" id="nrt_num_pasajeros" value="1">
                    <input type="hidden" id="nrt_num_adultos" value="1">
                    <input type="hidden" id="nrt_num_estudiantes" value="0">
                    <input type="hidden" id="nrt_num_ninos" value="0">
                    {{-- Precio --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Precio Total (USD) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" id="nrt_precio_total" class="form-control" placeholder="0.00" min="0">
                        <div class="invalid-feedback" id="err_nrt_precio_total"></div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Descuento</label>
                        <input type="number" step="0.01" id="nrt_descuento" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fuente</label>
                        <select id="nrt_fuente" class="form-select">
                            @foreach(['Oficina','Web','WhatsApp','Email','Teléfono','Referido','Otro'] as $f)
                                <option value="{{ $f }}">{{ $f }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Notas --}}
                    <div class="col-12">
                        <label class="form-label">Notas</label>
                        <textarea id="nrt_notas" class="form-control" rows="2" placeholder="Observaciones..."></textarea>
                    </div>
                    <div class="col-12">
                        <hr class="my-1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-primary mb-0"><i class="ri-group-line me-1"></i>Pasajeros de la reserva</h6>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="nrt_btn_generar_pasajeros">
                                <i class="ri-layout-grid-line me-1"></i>Generar formularios
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cantidad de adultos</label>
                        <select id="nrt_cantidad_adultos" class="form-select">
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}" @selected($i === 1)>{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cantidad de estudiantes</label>
                        <select id="nrt_cantidad_estudiantes" class="form-select">
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cantidad de niños</label>
                        <select id="nrt_cantidad_ninos" class="form-select">
                            @for($i = 0; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-12 d-none" id="nrt_pasajeros_box">
                        <div id="nrt_pasajeros_container" class="row g-3"></div>
                    </div>
                    <div class="col-12">
                        <hr class="my-1">
                        <h6 class="text-info mb-2"><i class="ri-service-line me-1"></i>Addons disponibles</h6>
                        <div id="nrt_addons_container" class="row g-3"></div>
                        <small id="nrt_addons_empty" class="text-muted">No hay addons cargados para este tour.</small>
                    </div>
                    {{-- Pago Inicial --}}
                    <div class="col-12">
                        <hr class="my-1">
                        <h6 class="text-muted mb-2"><i class="ri-money-dollar-circle-line me-1"></i>Pago Inicial (opcional)</h6>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Monto</label>
                        <input type="number" step="0.01" id="nrt_pago_monto" class="form-control" placeholder="0.00" min="0">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Método de Pago</label>
                        <select id="nrt_pago_metodo" class="form-select">
                            <option value="">— Método —</option>
                            @foreach(\App\Models\Pago::metodosLabel() as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">N° Operación</label>
                        <input type="text" id="nrt_pago_operacion" class="form-control" placeholder="Código Yape, N° transferencia...">
                    </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border shadow-none bg-light-subtle mb-0">
                            <div class="card-header bg-light">
                                <h6 class="card-title mb-0"><i class="ri-file-list-3-line me-1"></i>Resumen de la reserva</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Tarifa por persona</span><strong>USD <span id="nrt_resumen_tarifa">0.00</span></strong></div>
                                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Personas</span><strong><span id="nrt_resumen_personas">1</span></strong></div>
                                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Subtotal</span><strong>USD <span id="nrt_resumen_subtotal">0.00</span></strong></div>
                                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Items de addons</span><strong><span id="nrt_resumen_addons_items">0</span></strong></div>
                                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Addons</span><strong>USD <span id="nrt_resumen_addons">0.00</span></strong></div>
                                <div id="nrt_resumen_addons_detalle" class="small text-muted border rounded bg-white p-2 mb-2">No hay addons seleccionados.</div>
                                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Descuento</span><strong class="text-danger">USD <span id="nrt_resumen_descuento">0.00</span></strong></div>
                                <hr>
                                <div class="d-flex justify-content-between mb-2"><span class="fw-semibold">Total de la reserva</span><strong class="text-success">USD <span id="nrt_resumen_total">0.00</span></strong></div>
                                <div class="d-flex justify-content-between small mb-2"><span class="text-muted">Pago inicial</span><strong>USD <span id="nrt_resumen_inicial">0.00</span></strong></div>
                                <div class="d-flex justify-content-between small"><span class="text-muted">Saldo pendiente</span><strong>USD <span id="nrt_resumen_saldo">0.00</span></strong></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarNRT">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="nrtSpinner"></span>
                    <i class="ri-save-line me-1"></i> Guardar Reserva
                </button>
            </div>
        </div>
    </div>
</div>

@include('layouts.customizer')
@include('layouts.vendor-scripts')

<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
    let currentAjaxUrl    = '';
    let currentCalendario = '';
    let currentAddonsUrl  = '';

    function badgeTipoReserva(tipo) {
        if (tipo === 'estudiante') return 'Estudiante';
        if (tipo === 'nino') return 'Nino';
        return 'Adulto';
    }

    function cardPasajeroReserva(index, tipo) {
        return `
            <div class="col-12" data-tipo="${tipo}">
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Pasajero ${index + 1}</h6>
                        <span class="badge bg-light text-dark border">${badgeTipoReserva(tipo)}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3"><label class="form-label">Nombre</label><input type="text" class="form-control" data-key="nombre"></div>
                        <div class="col-md-3"><label class="form-label">Apellido</label><input type="text" class="form-control" data-key="apellido"></div>
                        <div class="col-md-2"><label class="form-label">Tipo Doc.</label><select class="form-select" data-key="tipo_documento"><option value="passport">Passport</option><option value="dni">DNI</option><option value="id">ID</option></select></div>
                        <div class="col-md-2"><label class="form-label">Documento</label><input type="text" class="form-control" data-key="numero_documento"></div>
                        <div class="col-md-2"><label class="form-label">Genero</label><select class="form-select" data-key="genero"><option value="">-</option><option value="male">Masculino</option><option value="female">Femenino</option><option value="other">Otro</option></select></div>
                        <div class="col-md-3"><label class="form-label">Fecha Nacimiento</label><input type="text" class="form-control flatpickr-date" data-date-format="Y-m-d" data-key="fecha_nacimiento"></div>
                        <div class="col-md-3"><label class="form-label">Email</label><input type="email" class="form-control" data-key="email"></div>
                        <div class="col-md-3"><label class="form-label">Telefono</label><input type="text" class="form-control" data-key="telefono"></div>
                        <div class="col-md-3"><label class="form-label">WhatsApp</label><input type="text" class="form-control" data-key="whatsapp"></div>
                    </div>
                </div>
            </div>
        `;
    }

    function sincronizarContadoresNRT() {
        const adultos = parseInt(document.getElementById('nrt_cantidad_adultos').value || '0', 10);
        const estudiantes = parseInt(document.getElementById('nrt_cantidad_estudiantes').value || '0', 10);
        const ninos = parseInt(document.getElementById('nrt_cantidad_ninos').value || '0', 10);
        document.getElementById('nrt_num_adultos').value = adultos + estudiantes;
        document.getElementById('nrt_num_estudiantes').value = estudiantes;
        document.getElementById('nrt_num_ninos').value = ninos;
        document.getElementById('nrt_num_pasajeros').value = adultos + estudiantes + ninos;
        actualizarResumenNRT();
    }

    function actualizarResumenNRT() {
        const precio = parseFloat(document.getElementById('nrt_precio_total').value || '0');
        const descuento = parseFloat(document.getElementById('nrt_descuento').value || '0');
        const pagoInicial = parseFloat(document.getElementById('nrt_pago_monto').value || '0');
        const personas = parseInt(document.getElementById('nrt_num_pasajeros').value || '0', 10);
        const subtotal = precio * personas;
        const addonsSeleccionados = Array.from(document.querySelectorAll('.nrt-addon-cantidad')).map((select) => {
            const monto = parseFloat(select.dataset.monto || '0');
            const qty = parseInt(select.value || '0', 10);
            return {
                nombre: select.dataset.nombre || 'Addon',
                qty,
                total: monto * qty,
            };
        }).filter((item) => item.qty > 0);
        const addonsTotal = addonsSeleccionados.reduce((acc, item) => acc + item.total, 0);
        const addonsItems = addonsSeleccionados.reduce((acc, item) => acc + item.qty, 0);
        const total = Math.max(0, subtotal + addonsTotal - descuento);
        const saldo = Math.max(0, total - pagoInicial);
        document.getElementById('nrt_resumen_tarifa').textContent = precio.toFixed(2);
        document.getElementById('nrt_resumen_personas').textContent = personas;
        document.getElementById('nrt_resumen_subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('nrt_resumen_addons_items').textContent = addonsItems;
        document.getElementById('nrt_resumen_addons').textContent = addonsTotal.toFixed(2);
        document.getElementById('nrt_resumen_addons_detalle').innerHTML = addonsSeleccionados.length
            ? addonsSeleccionados.map((item) => `<div class="d-flex justify-content-between gap-2 mb-1"><span>${item.nombre} x ${item.qty}</span><strong>USD ${item.total.toFixed(2)}</strong></div>`).join('')
            : 'No hay addons seleccionados.';
        document.getElementById('nrt_resumen_descuento').textContent = descuento.toFixed(2);
        document.getElementById('nrt_resumen_total').textContent = total.toFixed(2);
        document.getElementById('nrt_resumen_inicial').textContent = pagoInicial.toFixed(2);
        document.getElementById('nrt_resumen_saldo').textContent = saldo.toFixed(2);
    }

    function generarPasajerosNRT() {
        const container = document.getElementById('nrt_pasajeros_container');
        const box = document.getElementById('nrt_pasajeros_box');
        const cantidades = [
            { tipo: 'adulto', cantidad: parseInt(document.getElementById('nrt_cantidad_adultos').value || '0', 10) },
            { tipo: 'estudiante', cantidad: parseInt(document.getElementById('nrt_cantidad_estudiantes').value || '0', 10) },
            { tipo: 'nino', cantidad: parseInt(document.getElementById('nrt_cantidad_ninos').value || '0', 10) },
        ];
        let html = '';
        let index = 0;
        cantidades.forEach((grupo) => {
            for (let i = 0; i < grupo.cantidad; i++) {
                html += cardPasajeroReserva(index, grupo.tipo);
                index++;
            }
        });
        container.innerHTML = html;
        box.classList.toggle('d-none', index === 0);
        container.querySelectorAll('.flatpickr-date').forEach((input) => {
            if (window.flatpickr && !input._flatpickr) {
                window.flatpickr(input, { altInput: true, altFormat: 'd/m/Y', dateFormat: 'Y-m-d', allowInput: true, locale: 'es' });
            }
        });
        sincronizarContadoresNRT();
    }

    function renderAddonsNRT(addons) {
        const container = document.getElementById('nrt_addons_container');
        const empty = document.getElementById('nrt_addons_empty');
        container.innerHTML = '';
        if (!addons.length) {
            empty.textContent = 'No hay addons disponibles para este tour.';
            actualizarResumenNRT();
            return;
        }
        empty.textContent = '';
        addons.forEach((addon, index) => {
            container.insertAdjacentHTML('beforeend', `
                <div class="col-12">
                    <div class="border rounded p-3">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-8">
                                <div class="fw-semibold mb-1">${addon.nombre}</div>
                                <p class="text-muted small mb-2">${addon.descripcion ?? 'Sin descripción'}</p>
                                <div class="small text-primary">USD ${parseFloat(addon.monto).toFixed(2)} por unidad</div>
                            </div>
                            <div class="col-md-4">
                                <input type="hidden" name="nrt_addons[${index}][addon_id]" class="nrt-addon-id-input" value="${addon.id}" disabled>
                                <label class="form-label small">Cantidad</label>
                                <select class="form-select nrt-addon-cantidad" data-monto="${addon.monto}" data-nombre="${addon.nombre}">
                                    ${Array.from({ length: 11 }, (_, qty) => `<option value="${qty}">${qty}</option>`).join('')}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        });
        container.querySelectorAll('.nrt-addon-cantidad').forEach((select) => {
            select.addEventListener('change', function () {
                const idInput = this.closest('.border').querySelector('.nrt-addon-id-input');
                idInput.disabled = parseInt(this.value || '0', 10) === 0;
                actualizarResumenNRT();
            });
        });
        actualizarResumenNRT();
    }

    async function cargarAddonsNRT() {
        if (!currentAddonsUrl) return;
        try {
            const res = await fetch(currentAddonsUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            renderAddonsNRT(data);
        } catch (e) {
            document.getElementById('nrt_addons_empty').textContent = 'No se pudieron cargar los addons.';
        }
    }

    function pasajerosPayloadNRT() {
        return Array.from(document.querySelectorAll('#nrt_pasajeros_container [data-tipo]')).map((wrapper) => {
            const data = { tipo_pasajero: wrapper.dataset.tipo };
            wrapper.querySelectorAll('[data-key]').forEach((input) => {
                data[input.dataset.key] = input.value || null;
            });
            return data;
        });
    }

    function addonsPayloadNRT() {
        return Array.from(document.querySelectorAll('.nrt-addon-cantidad'))
            .map((select) => ({
                addon_id: select.closest('.border').querySelector('.nrt-addon-id-input')?.value,
                cantidad: select.value,
            }))
            .filter((item) => parseInt(item.cantidad || '0', 10) > 0);
    }

    // Open modal when clicking "Nueva Reserva"
    document.querySelectorAll('.btn-nueva-reserva').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tourId     = this.dataset.tourId;
            const tourNombre = this.dataset.tourNombre;
            const precio     = this.dataset.tourPrecio;
            const dias       = parseInt(this.dataset.tourDias) || 0;

            currentAjaxUrl    = this.dataset.ajaxUrl;
            currentCalendario = this.dataset.calendarioUrl;
            currentAddonsUrl  = this.dataset.addonsUrl || '';

            // Reset form
            document.getElementById('nrtTourNombre').textContent = tourNombre;
            document.getElementById('nrt_cliente').value        = '';
            document.getElementById('nrt_agente').value         = '';
            if (window.setFlatpickrDate) {
                window.setFlatpickrDate(document.getElementById('nrt_fecha_inicio'), '');
                window.setFlatpickrDate(document.getElementById('nrt_fecha_fin'), '');
            } else {
                document.getElementById('nrt_fecha_inicio').value = '';
                document.getElementById('nrt_fecha_fin').value = '';
            }
            document.getElementById('nrt_num_pasajeros').value  = 1;
            document.getElementById('nrt_num_adultos').value    = 1;
            document.getElementById('nrt_num_ninos').value      = 0;
            document.getElementById('nrt_precio_total').value   = precio ? parseFloat(precio).toFixed(2) : '';
            document.getElementById('nrt_descuento').value      = 0;
            document.getElementById('nrt_notas').value          = '';
            document.getElementById('nrt_pago_monto').value     = '';
            document.getElementById('nrt_pago_metodo').value    = '';
            document.getElementById('nrt_pago_operacion').value = '';
            document.getElementById('nrt_num_estudiantes').value = '0';
            document.getElementById('nrt_cantidad_adultos').value = '1';
            document.getElementById('nrt_cantidad_estudiantes').value = '0';
            document.getElementById('nrt_cantidad_ninos').value = '0';
            document.getElementById('nrt_pasajeros_container').innerHTML = '';
            document.getElementById('nrt_pasajeros_box').classList.add('d-none');
            document.getElementById('nrt_addons_container').innerHTML = '';
            document.getElementById('nrt_addons_empty').textContent = 'No hay addons cargados para este tour.';
            document.getElementById('nrtAlertError').classList.add('d-none');

            // Auto-calc fecha_fin when fecha_inicio changes
            const fi = document.getElementById('nrt_fecha_inicio');
            const syncFechaFin = function () {
                if (dias && this.value) {
                    const d = new Date(this.value + 'T00:00:00');
                    d.setDate(d.getDate() + dias - 1);
                    const fechaFin = d.toISOString().slice(0, 10);
                    if (window.setFlatpickrDate) {
                        window.setFlatpickrDate(document.getElementById('nrt_fecha_fin'), fechaFin);
                    } else {
                        document.getElementById('nrt_fecha_fin').value = fechaFin;
                    }
                }
            };
            fi.oninput = syncFechaFin;
            fi.onchange = syncFechaFin;

            sincronizarContadoresNRT();
            actualizarResumenNRT();
            cargarAddonsNRT();

            new bootstrap.Modal(document.getElementById('modalNuevaReservaTour')).show();
        });
    });

    // Save reservation via AJAX
    document.getElementById('btnGuardarNRT').addEventListener('click', async function () {
        const spinner = document.getElementById('nrtSpinner');
        spinner.classList.remove('d-none');
        this.disabled = true;
        document.getElementById('nrtAlertError').classList.add('d-none');

        const payload = {
            id_cliente            : document.getElementById('nrt_cliente').value,
            id_agente             : document.getElementById('nrt_agente').value || null,
            fecha_inicio          : document.getElementById('nrt_fecha_inicio').value,
            fecha_fin             : document.getElementById('nrt_fecha_fin').value || null,
            num_pasajeros         : document.getElementById('nrt_num_pasajeros').value,
            num_adultos           : document.getElementById('nrt_num_adultos').value,
            num_ninos             : document.getElementById('nrt_num_ninos').value,
            num_bebes             : 0,
            moneda                : 'USD',
            precio_total          : document.getElementById('nrt_precio_total').value,
            descuento             : document.getElementById('nrt_descuento').value || 0,
            notas                 : document.getElementById('nrt_notas').value || null,
            fuente_reserva        : document.getElementById('nrt_fuente').value,
            pago_inicial_monto    : document.getElementById('nrt_pago_monto').value || null,
            pago_inicial_metodo   : document.getElementById('nrt_pago_metodo').value || null,
            pago_inicial_operacion: document.getElementById('nrt_pago_operacion').value || null,
            pasajeros             : pasajerosPayloadNRT(),
            addons                : addonsPayloadNRT(),
        };

        try {
            const res = await fetch(currentAjaxUrl, {
                method : 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body   : JSON.stringify(payload),
            });

            const data = await res.json();

            if (data.ok) {
                bootstrap.Modal.getInstance(document.getElementById('modalNuevaReservaTour')).hide();
                // Redirect to tour calendar
                window.location.href = currentCalendario;
            } else {
                let msg = data.message || 'Error al guardar la reserva.';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join('<br>');
                }
                const alertEl = document.getElementById('nrtAlertError');
                alertEl.innerHTML = msg;
                alertEl.classList.remove('d-none');
            }
        } catch (e) {
            document.getElementById('nrtAlertError').textContent = 'Error de conexión. Intente nuevamente.';
            document.getElementById('nrtAlertError').classList.remove('d-none');
        } finally {
            spinner.classList.add('d-none');
            document.getElementById('btnGuardarNRT').disabled = false;
        }
    });

    document.getElementById('nrt_btn_generar_pasajeros')?.addEventListener('click', generarPasajerosNRT);
    ['nrt_cantidad_adultos', 'nrt_cantidad_estudiantes', 'nrt_cantidad_ninos'].forEach((id) => {
        document.getElementById(id)?.addEventListener('change', sincronizarContadoresNRT);
    });
    ['nrt_precio_total', 'nrt_descuento', 'nrt_pago_monto'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', actualizarResumenNRT);
    });
})();
</script>

<script>
    // Confirmación de eliminación
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tourName = this.getAttribute('data-name');
            if (confirm(`¿Estás seguro de eliminar el tour "${tourName}"?\n\nEsta acción no se puede deshacer.`)) {
                this.closest('form').submit();
            }
        });
    });
</script>

</body>
</html>
