@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Detalle del Tour']); ?>
    @include('layouts.head-css')
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="ri-check-line me-1"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ri-arrow-left-line"></i>
                        </a>
                        <div>
                            <h4 class="mb-0">{{ $tour->nombre_tour }}</h4>
                            <span class="text-muted small">{{ $tour->codigo_tour }}</span>
                        </div>
                        <span class="badge {{ $tour->estado ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }}">
                            {{ $tour->estado ? 'Activo' : 'Inactivo' }}
                        </span>
                        @if($tour->destacado)
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                <i class="ri-star-fill me-1"></i>Destacado
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.tours.calendar.create', $tour) }}" class="btn btn-primary btn-sm">
                            <i class="ri-calendar-add-line me-1"></i> Agregar Año
                        </a>
                        <a href="{{ route('admin.tours.edit', $tour) }}" class="btn btn-warning btn-sm">
                            <i class="ri-pencil-line me-1"></i> Editar Tour
                        </a>
                    </div>
                </div>

                <div class="row g-3 mb-4">

                    <!-- Info General -->
                    <div class="col-md-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="ri-information-line me-1"></i>Información General</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th width="200">Duración</th>
                                            <td>
                                                {{ $tour->duracion_dias ?? '—' }} días
                                                / {{ $tour->duracion_noches ?? '—' }} noches
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Nivel Dificultad</th>
                                            <td>{{ $tour->nivel_dificultad ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Precio Base</th>
                                            <td>
                                                @if($tour->precio_base)
                                                    {{ $tour->moneda }} {{ number_format($tour->precio_base, 2) }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Capacidad</th>
                                            <td>
                                                Mín: {{ $tour->min_personas ?? '—' }} /
                                                Máx: {{ $tour->max_personas ?? '—' }} personas
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Salida desde</th>
                                            <td>{{ $tour->salida_desde ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Destino Principal</th>
                                            <td>{{ $tour->destino_principal ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Categorías</th>
                                            <td>
                                                @forelse($tour->categorias as $cat)
                                                    <span class="badge"
                                                          style="background:{{ $cat->color ?? '#6c757d' }}">
                                                        {{ $cat->nombre }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted">Sin categorías</span>
                                                @endforelse
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas de disponibilidad -->
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="ri-calendar-2-line me-1"></i>Calendarios</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="fs-2 fw-semibold text-primary">{{ $tour->calendarYears->count() }}</div>
                                    <div class="text-muted">Años configurados</div>
                                </div>
                                @if($tour->max_personas)
                                    <div class="alert alert-info py-2 small">
                                        <i class="ri-group-line me-1"></i>
                                        Capacidad diaria: <strong>{{ $tour->max_personas }}</strong> espacios/día
                                    </div>
                                @endif
                                <a href="{{ route('admin.tours.calendar.create', $tour) }}"
                                   class="btn btn-outline-primary btn-sm w-100">
                                    <i class="ri-calendar-add-line me-1"></i> Agregar nuevo año
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Descripciones -->
                @if($tour->descripcion_corta || $tour->incluye || $tour->no_incluye)
                    <div class="row g-3 mb-4">
                        @if($tour->descripcion_corta)
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header"><h5 class="card-title mb-0">Descripción</h5></div>
                                    <div class="card-body">{{ $tour->descripcion_corta }}</div>
                                </div>
                            </div>
                        @endif
                        @if($tour->incluye || $tour->no_incluye)
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-success-subtle">
                                        <h5 class="card-title mb-0 text-success"><i class="ri-check-line me-1"></i>Incluye</h5>
                                    </div>
                                    <div class="card-body" style="white-space:pre-line">{{ $tour->incluye ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-danger-subtle">
                                        <h5 class="card-title mb-0 text-danger"><i class="ri-close-line me-1"></i>No Incluye</h5>
                                    </div>
                                    <div class="card-body" style="white-space:pre-line">{{ $tour->no_incluye ?? '—' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mb-4">
                    <div id="tour-pricing-panel" data-panel-url="{{ route('admin.tours.pricing.panel', $tour) }}">
                        @include('admin.tours.partials.pricing-panel', ['tour' => $tour])
                    </div>
                </div>

                <!-- Calendarios por año -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="ri-calendar-2-line me-1"></i>Disponibilidad por Año
                        </h5>
                        <a href="{{ route('admin.tours.calendar.create', $tour) }}" class="btn btn-primary btn-sm">
                            <i class="ri-add-line me-1"></i>Agregar Año
                        </a>
                    </div>
                    <div class="card-body">

                        @if($tour->calendarYears->isEmpty())
                            <div class="text-center text-muted py-5">
                                <i class="ri-calendar-x-line fs-1 d-block mb-2"></i>
                                No hay calendarios generados para este tour.
                                <div class="mt-3">
                                    <a href="{{ route('admin.tours.calendar.create', $tour) }}"
                                       class="btn btn-primary btn-sm">
                                        <i class="ri-calendar-add-line me-1"></i>Generar primer año
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row g-3">
                                @foreach($tour->calendarYears->sortByDesc('anio') as $calendar)
                                    <div class="col-md-4 col-lg-3">
                                        <div class="card border shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="fs-3 fw-bold text-primary">{{ $calendar->anio }}</div>
                                                        <div class="text-muted small">
                                                            {{ $calendar->total_dias }} días
                                                            @if($calendar->es_bisiesto)
                                                                <span class="badge bg-info-subtle text-info border border-info-subtle">Bisiesto</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        @if($calendar->capacidad_anio)
                                                            <div class="fw-medium">{{ number_format($calendar->capacidad_anio) }}</div>
                                                            <div class="text-muted" style="font-size:.75rem">esp/día</div>
                                                        @else
                                                            <div class="text-muted small">Default del tour</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 mt-3">
                                                    <a href="{{ route('admin.tours.calendar.show', [$tour, $calendar]) }}"
                                                       class="btn btn-sm btn-outline-primary flex-fill">
                                                        <i class="ri-calendar-line me-1"></i>Ver calendario
                                                    </a>
                                                    <form action="{{ route('admin.tours.calendar.destroy', [$tour, $calendar]) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('¿Eliminar el calendario {{ $calendar->anio }}? Se borrarán todos los días.')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Pasajeros vinculados</h5>
                            </div>
                            <div class="card-body">
                                @forelse($tour->pasajeros->take(8) as $pasajero)
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <div>
                                            <a href="{{ route('admin.pasajeros.show', $pasajero) }}" class="fw-medium">{{ $pasajero->nombre_completo }}</a>
                                            <div class="text-muted small">{{ $pasajero->numero_documento }}</div>
                                        </div>
                                        <a href="{{ route('admin.clientes.show', $pasajero->cliente) }}" class="small">Cliente</a>
                                    </div>
                                @empty
                                    <div class="text-muted">Sin pasajeros vinculados.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Reservas recientes</h5>
                            </div>
                            <div class="card-body">
                                @forelse($tour->reservas->take(8) as $reserva)
                                    <div class="d-flex justify-content-between border-bottom py-2">
                                        <div>
                                            <a href="{{ route('admin.reservas.show', $reserva) }}" class="fw-medium">{{ $reserva->codigo_reserva }}</a>
                                            <div class="text-muted small">{{ $reserva->cliente?->nombre_completo ?? 'Sin cliente' }}</div>
                                        </div>
                                        <span class="small">{{ $reserva->fecha_inicio?->format('d/m/Y') }}</span>
                                    </div>
                                @empty
                                    <div class="text-muted">Sin reservas vinculadas.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>

@include('layouts.customizer')
@include('layouts.vendor-scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
<style>
    #tour-pricing-panel [data-sortable-row="1"] {
        cursor: move;
    }

    #tour-pricing-panel [data-sortable-row="1"].is-dragging {
        opacity: .55;
    }

    #tour-pricing-panel .drag-handle {
        cursor: grab;
        width: 28px;
        height: 28px;
    }
</style>
<script>
    (() => {
        const panel = document.getElementById('tour-pricing-panel');

        if (!panel) {
            return;
        }

        const getHeaders = (form) => ({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
        });

        const getErrorMessage = async (response) => {
            try {
                const payload = await response.json();

                if (payload?.errors) {
                    return Object.values(payload.errors).flat().join('\n');
                }

                if (payload?.message) {
                    return payload.message;
                }
            } catch (error) {
            }

            return 'No se pudo guardar la configuracion de precios.';
        };

        const toggleButtonLoading = (button, isLoading) => {
            if (!button) {
                return;
            }

            button.disabled = isLoading;
            button.querySelector('.button-default')?.classList.toggle('d-none', isLoading);
            button.querySelector('.button-loading')?.classList.toggle('d-none', !isLoading);
            button.querySelector('.button-success')?.classList.add('d-none');
        };

        const showButtonSuccess = (button) => {
            if (!button || !button.querySelector('.button-success')) {
                return;
            }

            button.querySelector('.button-default')?.classList.add('d-none');
            button.querySelector('.button-loading')?.classList.add('d-none');
            button.querySelector('.button-success')?.classList.remove('d-none');

            window.setTimeout(() => {
                button.querySelector('.button-success')?.classList.add('d-none');
                button.querySelector('.button-default')?.classList.remove('d-none');
            }, 1600);
        };

        const syncRowOrder = (tbody) => {
            Array.from(tbody.querySelectorAll('tr[data-sortable-row="1"]')).forEach((row, index) => {
                const orderInput = row.querySelector('input[name$="[orden]"]');

                if (orderInput) {
                    orderInput.value = index + 1;
                }
            });
        };

        const getDragAfterElement = (tbody, clientY) => {
            const draggableRows = [...tbody.querySelectorAll('tr[data-sortable-row="1"]:not(.is-dragging)')];

            return draggableRows.reduce((closest, row) => {
                const box = row.getBoundingClientRect();
                const offset = clientY - box.top - (box.height / 2);

                if (offset < 0 && offset > closest.offset) {
                    return { offset, element: row };
                }

                return closest;
            }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
        };

        const initSortableRows = () => {
            panel.querySelectorAll('tbody[data-sortable-rows="1"]').forEach((tbody) => {
                if (tbody.dataset.sortableReady === '1') {
                    return;
                }

                tbody.dataset.sortableReady = '1';

                tbody.addEventListener('dragstart', (event) => {
                    const row = event.target.closest('tr[data-sortable-row="1"]');

                    if (!row) {
                        return;
                    }

                    row.classList.add('is-dragging');
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', row.rowIndex.toString());
                });

                tbody.addEventListener('dragend', (event) => {
                    const row = event.target.closest('tr[data-sortable-row="1"]');

                    if (!row) {
                        return;
                    }

                    row.classList.remove('is-dragging');
                    syncRowOrder(tbody);
                });

                tbody.addEventListener('dragover', (event) => {
                    event.preventDefault();

                    const draggingRow = tbody.querySelector('.is-dragging');

                    if (!draggingRow) {
                        return;
                    }

                    const afterElement = getDragAfterElement(tbody, event.clientY);

                    if (!afterElement) {
                        tbody.appendChild(draggingRow);
                        return;
                    }

                    if (afterElement !== draggingRow) {
                        tbody.insertBefore(draggingRow, afterElement);
                    }
                });

                syncRowOrder(tbody);
            });
        };

        initSortableRows();

        panel.addEventListener('submit', async (event) => {
            const form = event.target.closest('form[data-pricing-ajax]');

            if (!form) {
                return;
            }

            event.preventDefault();

            const intendedMethod = (form.dataset.httpMethod || form.method || 'POST').toUpperCase();
            const confirmMessage = form.dataset.confirm;
            const submitButtons = form.querySelectorAll('button[type="submit"]');
            const submitter = event.submitter;
            const successButtonSelector = submitter?.querySelector('.button-success')
                ? `form[action="${form.action}"] button[type="submit"]`
                : null;

            if (confirmMessage && !window.confirm(confirmMessage)) {
                return;
            }

            submitButtons.forEach((button) => {
                if (button !== submitter) {
                    button.disabled = true;
                }
            });
            toggleButtonLoading(submitter, true);

            try {
                const payload = new FormData(form);

                if (intendedMethod !== 'POST') {
                    payload.set('_method', intendedMethod);
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: getHeaders(form),
                    body: payload,
                });

                if (!response.ok) {
                    throw new Error(await getErrorMessage(response));
                }

                const data = await response.json();
                panel.innerHTML = data.html;
                initSortableRows();

                if (successButtonSelector) {
                    showButtonSuccess(panel.querySelector(successButtonSelector));
                }
            } catch (error) {
                window.alert(error.message || 'No se pudo guardar la configuracion de precios.');
            } finally {
                submitButtons.forEach((button) => button.disabled = false);
                toggleButtonLoading(submitter, false);
            }
        });
    })();
</script>
</body>
</html>
