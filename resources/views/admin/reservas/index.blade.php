@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Lista de Reservas']); ?>

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
                                <h4 class="card-title mb-0 flex-grow-1">Lista de Reservas</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.reservas.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-middle"></i> Nueva Reserva
                                    </a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <!-- ==================== FILTROS DE BÚSQUEDA ==================== -->
                                <form method="GET" action="{{ route('admin.reservas.index') }}" class="mb-4" data-auto-filter="true">
                                    <div class="row g-3">

                                        <!-- Código de Reserva -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Código Reserva</label>
                                            <input type="text" name="codigo_reserva" class="form-control"
                                                   value="{{ request('codigo_reserva') }}" placeholder="RES-20260327-0001">
                                        </div>

                                        <!-- Cliente -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Cliente</label>
                                            <input type="text" name="cliente" class="form-control"
                                                   value="{{ request('cliente') }}" placeholder="Nombre o DNI">
                                        </div>

                                        <!-- Estado de la Reserva -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Estado</label>
                                            <select name="estado" class="form-select">
                                                <option value="">Todos los estados</option>
                                                <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                                <option value="confirmada" {{ request('estado') === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                                <option value="pagada" {{ request('estado') === 'pagada' ? 'selected' : '' }}>Pagada</option>
                                                <option value="cancelada" {{ request('estado') === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                                <option value="reembolsada" {{ request('estado') === 'reembolsada' ? 'selected' : '' }}>Reembolsada</option>
                                                <option value="completada" {{ request('estado') === 'completada' ? 'selected' : '' }}>Completada</option>
                                            </select>
                                        </div>

                                        <!-- Fecha de Inicio -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Fecha Inicio</label>
                                            <input type="date" name="fecha_inicio" class="form-control"
                                                   value="{{ request('fecha_inicio') }}">
                                        </div>

                                        <!-- Tipo de Reserva -->
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Tipo de Reserva</label>
                                            <input type="text" name="tipo_reserva" class="form-control"
                                                   value="{{ request('tipo_reserva') }}" placeholder="Tour, Hotel, Vuelo...">
                                        </div>

                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-search-line"></i> Buscar
                                            </button>
                                            <a href="{{ route('admin.reservas.index') }}" class="btn btn-secondary ms-2">
                                                <i class="ri-refresh-line"></i> Limpiar Filtros
                                            </a>
                                        </div>
                                    </div>
                                </form>
                                <!-- ======================================================== -->

                                <div class="live-preview">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0 table-hover">
                                            <thead class="table-light">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Código</th>
                                                <th scope="col">Cliente</th>
                                                <th scope="col">Fecha Reserva</th>
                                                <th scope="col">Servicio</th>
                                                <th scope="col">Fecha Viaje</th>
                                                <th scope="col">Pasajeros</th>
                                                <th scope="col">Precio Final</th>
                                                <th scope="col">Estado</th>
                                                <th scope="col" width="140">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse ($reservas as $reserva)
                                                <tr>
                                                    <td>{{ $reserva->id }}</td>
                                                    <td>
                                                        <a href="{{ route('admin.reservas.show', $reserva) }}" class="fw-semibold text-primary">
                                                            {{ $reserva->codigo_reserva }}
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <strong>{{ $reserva->cliente->nombre_completo ?? $reserva->cliente->nombre . ' ' . $reserva->cliente->apellido }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $reserva->cliente->numero_documento ?? 'Sin documento' }}</small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <small>{{ $reserva->fecha_reserva->format('d/m/Y H:i') }}</small>
                                                    </td>
                                                    <td>
                                                        <strong>{{ $reserva->tipo_reserva }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $reserva->descripcion_servicio ?? '—' }}</small>
                                                    </td>
                                                    <td>
                                                        {{ $reserva->fecha_inicio->format('d/m/Y') }}
                                                        @if($reserva->fecha_fin)
                                                            <br><small>hasta {{ $reserva->fecha_fin->format('d/m/Y') }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <strong>{{ $reserva->num_pasajeros }}</strong>
                                                        <small class="text-muted d-block">
                                                            ({{ $reserva->num_adultos }} adultos
                                                            @if($reserva->num_ninos > 0)
                                                                + {{ $reserva->num_ninos }} niños
                                                            @endif
                                                            @if($reserva->num_bebes > 0)
                                                                + {{ $reserva->num_bebes }} bebés
                                                            @endif
                                                            )
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <strong>
                                                            USD {{ number_format($reserva->precio_final, 2) }}
                                                        </strong>
                                                        @if($reserva->descuento > 0)
                                                            <br>
                                                            <small class="text-danger">-{{ number_format($reserva->descuento, 2) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $estadoClass = match($reserva->estado_reserva) {
                                                                'pendiente'   => 'bg-warning',
                                                                'confirmada'  => 'bg-info',
                                                                'pagada'      => 'bg-success',
                                                                'cancelada'   => 'bg-danger',
                                                                'reembolsada' => 'bg-secondary',
                                                                'completada'  => 'bg-primary',
                                                                default       => 'bg-secondary'
                                                            };
                                                        @endphp
                                                        <span class="badge {{ $estadoClass }} px-3 py-2">
                                                            {{ ucfirst($reserva->estado_reserva) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <a href="{{ route('admin.reservas.show', $reserva) }}"
                                                               class="btn btn-sm btn-soft-info" title="Ver detalle">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            <a href="{{ route('admin.reservas.edit', $reserva) }}"
                                                               class="btn btn-sm btn-soft-warning" title="Editar">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                            <form action="{{ route('admin.reservas.destroy', $reserva) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-soft-danger delete-btn"
                                                                        title="Eliminar"
                                                                        data-name="{{ $reserva->codigo_reserva }}">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center py-5">
                                                        <i class="ri-bookmark-line display-4 text-muted"></i>
                                                        <p class="mt-3 mb-0">No hay reservas registradas</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginación -->
                                    @if($reservas->hasPages())
                                        <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                                            <div class="text-muted small">
                                                Mostrando <strong>{{ $reservas->firstItem() ?? 0 }}</strong> al
                                                <strong>{{ $reservas->lastItem() ?? 0 }}</strong> de
                                                <strong>{{ $reservas->total() }}</strong> reservas
                                            </div>
                                            <nav aria-label="Page navigation">
                                                {{ $reservas->links('pagination::bootstrap-5') }}
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

<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
    // Confirmación de eliminación
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const name = this.getAttribute('data-name');
            if (confirm(`¿Estás seguro de eliminar la reserva "${name}"?\n\nEsta acción no se puede deshacer.`)) {
                this.closest('form').submit();
            }
        });
    });
</script>

</body>
</html>
