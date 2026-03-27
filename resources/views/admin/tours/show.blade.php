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
                        @php
                            $estadoClases = [
                                'Activo'    => 'bg-success-subtle text-success border border-success-subtle',
                                'Inactivo'  => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                'Agotado'   => 'bg-warning-subtle text-warning border border-warning-subtle',
                                'Cancelado' => 'bg-danger-subtle text-danger border border-danger-subtle',
                            ];
                        @endphp
                        <span class="badge {{ $estadoClases[$tour->estado] ?? '' }}">{{ $tour->estado }}</span>
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

            </div>
        </div>
        @include('layouts.footer')
    </div>
</div>

@include('layouts.customizer')
@include('layouts.vendor-scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
