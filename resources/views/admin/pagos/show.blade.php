@include('layouts.main')
<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Pago ' . $pago->codigo_pago]); ?>
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
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">Pago: <span class="text-primary">{{ $pago->codigo_pago }}</span></h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.pagos.index') }}">Pagos</a></li>
                                    <li class="breadcrumb-item active">{{ $pago->codigo_pago }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-3">

                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0"><i class="ri-bank-card-line me-2 text-primary"></i>Detalles del Pago</h5>
                                @php
                                    $estadoClass = match($pago->estado) {
                                        'confirmado' => 'bg-success', 'pendiente' => 'bg-warning',
                                        'rechazado' => 'bg-danger', 'devuelto' => 'bg-secondary',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $estadoClass }} px-3 py-2 fs-6">{{ ucfirst($pago->estado) }}</span>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <p class="text-muted small mb-1">Código de Pago</p>
                                        <p class="fw-bold fs-5 text-primary mb-0">{{ $pago->codigo_pago }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted small mb-1">Fecha de Pago</p>
                                        <p class="fw-semibold mb-0">{{ $pago->fecha_pago->format('d/m/Y') }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted small mb-1">Registrado</p>
                                        <p class="fw-semibold mb-0">{{ $pago->created_at->format('d/m/Y H:i') }}</p>
                                    </div>

                                    <div class="col-md-3">
                                        <p class="text-muted small mb-1">Monto</p>
                                        <p class="fw-bold fs-4 text-success mb-0">
                                            {{ $pago->moneda }} {{ number_format($pago->monto, 2) }}
                                        </p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="text-muted small mb-1">Tipo de Pago</p>
                                        <p class="fw-semibold mb-0">{{ $pago->tipo_texto }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="text-muted small mb-1">Método</p>
                                        <p class="fw-semibold mb-0">{{ $pago->metodo_texto }}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="text-muted small mb-1">N° Operación</p>
                                        <p class="fw-semibold mb-0">{{ $pago->numero_operacion ?? '—' }}</p>
                                    </div>

                                    @if($pago->banco_origen || $pago->banco_destino)
                                        <div class="col-md-6">
                                            <p class="text-muted small mb-1">Banco Origen</p>
                                            <p class="fw-semibold mb-0">{{ $pago->banco_origen ?? '—' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="text-muted small mb-1">Banco Destino</p>
                                            <p class="fw-semibold mb-0">{{ $pago->banco_destino ?? '—' }}</p>
                                        </div>
                                    @endif

                                    @if($pago->notas)
                                        <div class="col-12">
                                            <p class="text-muted small mb-1">Notas</p>
                                            <p class="mb-0">{{ $pago->notas }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Resumen de la reserva --}}
                        @if($pago->reserva)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-file-list-3-line me-2 text-info"></i>Estado de la Reserva</h5>
                                </div>
                                <div class="card-body">
                                    @include('admin.pagos.partials.saldo-resumen', ['rsv' => $pago->reserva])
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="{{ route('admin.reservas.show', $pago->reserva) }}" class="btn btn-sm btn-outline-info">
                                            <i class="ri-eye-line me-1"></i>Ver Reserva {{ $pago->reserva->codigo_reserva }}
                                        </a>
                                        @if($pago->reserva->saldo_pendiente > 0)
                                            <a href="{{ route('admin.pagos.create', ['reserva_id' => $pago->reserva->id]) }}"
                                               class="btn btn-sm btn-success">
                                                <i class="ri-add-line me-1"></i>Agregar Otro Pago
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-xl-4">
                        {{-- Cliente --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="ri-user-line me-2 text-success"></i>Cliente</h5>
                            </div>
                            <div class="card-body">
                                @if($pago->cliente)
                                    <p class="fw-bold mb-1">
                                        <a href="{{ route('admin.clientes.show', $pago->cliente) }}">
                                            {{ $pago->cliente->nombre_completo }}
                                        </a>
                                    </p>
                                    <p class="text-muted small mb-0">
                                        {{ strtoupper($pago->cliente->tipo_documento ?? '') }}: {{ $pago->cliente->numero_documento ?? '—' }}
                                    </p>
                                @else
                                    <p class="text-muted mb-0">Sin cliente</p>
                                @endif
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="card mt-3">
                            <div class="card-body d-grid gap-2">
                                <a href="{{ route('admin.pagos.edit', $pago) }}" class="btn btn-warning">
                                    <i class="ri-pencil-line me-1"></i>Editar Pago
                                </a>
                                <a href="{{ route('admin.pagos.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-list-check me-1"></i>Volver a Pagos
                                </a>
                                <form action="{{ route('admin.pagos.destroy', $pago) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este pago?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="ri-delete-bin-line me-1"></i>Eliminar
                                    </button>
                                </form>
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
</body>
</html>
