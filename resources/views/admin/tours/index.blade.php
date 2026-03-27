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
                                                <option value="Activo" {{ request('estado') === 'Activo' ? 'selected' : '' }}>Activo</option>
                                                <option value="Inactivo" {{ request('estado') === 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                                <option value="Agotado" {{ request('estado') === 'Agotado' ? 'selected' : '' }}>Agotado</option>
                                                <option value="Cancelado" {{ request('estado') === 'Cancelado' ? 'selected' : '' }}>Cancelado</option>
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
                                                        @php
                                                            $estadoClass = match($tour->estado) {
                                                                'Activo' => 'text-success',
                                                                'Inactivo' => 'text-muted',
                                                                'Agotado' => 'text-warning',
                                                                'Cancelado' => 'text-danger',
                                                                default => 'text-secondary'
                                                            };
                                                        @endphp
                                                        <span class="{{ $estadoClass }}">
                                                            <i class="ri-checkbox-circle-line fs-17 align-middle"></i>
                                                            {{ $tour->estado }}
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
                                                                    data-ajax-url="{{ route('admin.tours.reservas.store-ajax', $tour) }}">
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
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
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
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Inicio <span class="text-danger">*</span></label>
                        <input type="date" id="nrt_fecha_inicio" class="form-control">
                        <div class="invalid-feedback" id="err_nrt_fecha_inicio"></div>
                    </div>
                    {{-- Fecha Fin --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fecha Fin</label>
                        <input type="date" id="nrt_fecha_fin" class="form-control">
                    </div>
                    {{-- Pasajeros --}}
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Pasajeros</label>
                        <input type="number" id="nrt_num_pasajeros" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Adultos</label>
                        <input type="number" id="nrt_num_adultos" class="form-control" value="1" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Niños</label>
                        <input type="number" id="nrt_num_ninos" class="form-control" value="0" min="0">
                    </div>
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

    // Open modal when clicking "Nueva Reserva"
    document.querySelectorAll('.btn-nueva-reserva').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const tourId     = this.dataset.tourId;
            const tourNombre = this.dataset.tourNombre;
            const precio     = this.dataset.tourPrecio;
            const dias       = parseInt(this.dataset.tourDias) || 0;

            currentAjaxUrl    = this.dataset.ajaxUrl;
            currentCalendario = this.dataset.calendarioUrl;

            // Reset form
            document.getElementById('nrtTourNombre').textContent = tourNombre;
            document.getElementById('nrt_cliente').value        = '';
            document.getElementById('nrt_agente').value         = '';
            document.getElementById('nrt_fecha_inicio').value   = '';
            document.getElementById('nrt_fecha_fin').value      = '';
            document.getElementById('nrt_num_pasajeros').value  = 1;
            document.getElementById('nrt_num_adultos').value    = 1;
            document.getElementById('nrt_num_ninos').value      = 0;
            document.getElementById('nrt_precio_total').value   = precio ? parseFloat(precio).toFixed(2) : '';
            document.getElementById('nrt_descuento').value      = 0;
            document.getElementById('nrt_notas').value          = '';
            document.getElementById('nrt_pago_monto').value     = '';
            document.getElementById('nrt_pago_metodo').value    = '';
            document.getElementById('nrt_pago_operacion').value = '';
            document.getElementById('nrtAlertError').classList.add('d-none');

            // Auto-calc fecha_fin when fecha_inicio changes
            const fi = document.getElementById('nrt_fecha_inicio');
            fi.oninput = function () {
                if (dias && this.value) {
                    const d = new Date(this.value + 'T00:00:00');
                    d.setDate(d.getDate() + dias - 1);
                    document.getElementById('nrt_fecha_fin').value = d.toISOString().slice(0, 10);
                }
            };

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
