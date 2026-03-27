@include('layouts.main')
<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Lista de Pagos']); ?>
    @include('layouts.head-css')
</head>
<body>
<div id="layout-wrapper">
    @include('layouts.menu')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h4 class="card-title mb-0 flex-grow-1">Lista de Pagos</h4>
                                <a href="{{ route('admin.pagos.create') }}" class="btn btn-primary btn-sm">
                                    <i class="ri-add-line"></i> Registrar Pago
                                </a>
                            </div>
                            <div class="card-body">

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show">
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                {{-- Filtros --}}
                                <form method="GET" class="mb-4">
                                    <div class="row g-2">
                                        <div class="col-md-2">
                                            <input type="text" name="codigo_pago" class="form-control form-control-sm"
                                                   placeholder="Código pago" value="{{ request('codigo_pago') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="reserva" class="form-control form-control-sm"
                                                   placeholder="N° Reserva" value="{{ request('reserva') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="cliente" class="form-control form-control-sm"
                                                   placeholder="Cliente" value="{{ request('cliente') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <select name="metodo_pago" class="form-select form-select-sm">
                                                <option value="">Todos los métodos</option>
                                                @foreach(\App\Models\Pago::metodosLabel() as $val => $label)
                                                    <option value="{{ $val }}" {{ request('metodo_pago') === $val ? 'selected' : '' }}>{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <select name="estado" class="form-select form-select-sm">
                                                <option value="">Todos los estados</option>
                                                <option value="confirmado" {{ request('estado') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                                                <option value="pendiente"  {{ request('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
                                                <option value="rechazado"  {{ request('estado') === 'rechazado'  ? 'selected' : '' }}>Rechazado</option>
                                                <option value="devuelto"   {{ request('estado') === 'devuelto'   ? 'selected' : '' }}>Devuelto</option>
                                            </select>
                                        </div>
                                        <div class="col-md-1">
                                            <button class="btn btn-primary btn-sm w-100">
                                                <i class="ri-search-line"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-1">
                                        <div class="col-md-2">
                                            <input type="date" name="fecha_desde" class="form-control form-control-sm"
                                                   value="{{ request('fecha_desde') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="date" name="fecha_hasta" class="form-control form-control-sm"
                                                   value="{{ request('fecha_hasta') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary btn-sm">
                                                <i class="ri-refresh-line"></i> Limpiar
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            @if($totalPEN > 0)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 me-2">
                                                    Total PEN: S/ {{ number_format($totalPEN, 2) }}
                                                </span>
                                            @endif
                                            @if($totalUSD > 0)
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                                    Total USD: $ {{ number_format($totalUSD, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Código Pago</th>
                                            <th>Fecha</th>
                                            <th>Reserva / Tour</th>
                                            <th>Cliente</th>
                                            <th>Monto</th>
                                            <th>Tipo</th>
                                            <th>Método</th>
                                            <th>N° Operación</th>
                                            <th>Estado</th>
                                            <th width="100">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($pagos as $pago)
                                            <tr>
                                                <td><a href="{{ route('admin.pagos.show', $pago) }}" class="fw-semibold">{{ $pago->codigo_pago }}</a></td>
                                                <td><small>{{ $pago->fecha_pago->format('d/m/Y') }}</small></td>
                                                <td>
                                                    <a href="{{ route('admin.reservas.show', $pago->reserva) }}" class="text-primary small">
                                                        {{ $pago->reserva->codigo_reserva }}
                                                    </a>
                                                    @if($pago->reserva->tour)
                                                        <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($pago->reserva->tour->nombre_tour, 20) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $pago->cliente->nombre_completo ?? '—' }}</small>
                                                </td>
                                                <td>
                                                    <strong class="text-success">{{ $pago->moneda }} {{ number_format($pago->monto, 2) }}</strong>
                                                </td>
                                                <td><span class="badge bg-secondary-subtle text-secondary border">{{ $pago->tipo_texto }}</span></td>
                                                <td><small>{{ $pago->metodo_texto }}</small></td>
                                                <td><small class="text-muted">{{ $pago->numero_operacion ?? '—' }}</small></td>
                                                <td>
                                                    @php
                                                        $estadoClass = match($pago->estado) {
                                                            'confirmado' => 'bg-success',
                                                            'pendiente'  => 'bg-warning',
                                                            'rechazado'  => 'bg-danger',
                                                            'devuelto'   => 'bg-secondary',
                                                            default      => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $estadoClass }}">{{ ucfirst($pago->estado) }}</span>
                                                </td>
                                                <td>
                                                    <div class="hstack gap-1">
                                                        <a href="{{ route('admin.pagos.show', $pago) }}" class="btn btn-sm btn-soft-info" title="Ver">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <a href="{{ route('admin.pagos.edit', $pago) }}" class="btn btn-sm btn-soft-warning" title="Editar">
                                                            <i class="ri-pencil-line"></i>
                                                        </a>
                                                        <form action="{{ route('admin.pagos.destroy', $pago) }}" method="POST" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="button" class="btn btn-sm btn-soft-danger del-btn"
                                                                    data-codigo="{{ $pago->codigo_pago }}">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center py-5">
                                                    <i class="ri-bank-card-line display-4 text-muted"></i>
                                                    <p class="mt-3 mb-0">No hay pagos registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($pagos->hasPages())
                                    <div class="d-flex justify-content-between mt-4">
                                        <div class="text-muted small">
                                            Mostrando {{ $pagos->firstItem() }} al {{ $pagos->lastItem() }} de {{ $pagos->total() }} pagos
                                        </div>
                                        {{ $pagos->links('pagination::bootstrap-5') }}
                                    </div>
                                @endif

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
<script>
document.querySelectorAll('.del-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        if (confirm(`¿Eliminar el pago "${this.dataset.codigo}"?`)) {
            this.closest('form').submit();
        }
    });
});
</script>
</body>
</html>
