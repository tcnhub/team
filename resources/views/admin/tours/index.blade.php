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
                                <form method="GET" action="{{ route('admin.tours.index') }}" class="mb-4">
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
                                                            <strong>S/ {{ number_format($tour->precio_base, 2) }}</strong>
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

@include('layouts.customizer')
@include('layouts.vendor-scripts')

<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

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
