@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Reserva ' . $reserva->codigo_reserva]); ?>
    @include('layouts.head-css')
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
                            <h4 class="mb-0">Reserva: <span class="text-primary">{{ $reserva->codigo_reserva }}</span></h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.reservas.index') }}">Reservas</a></li>
                                    <li class="breadcrumb-item active">{{ $reserva->codigo_reserva }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="ri-checkbox-circle-line me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="ri-error-warning-line me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row g-3">

                    {{-- ── Columna izquierda: info principal ── --}}
                    <div class="col-xl-8">

                        {{-- Card: Datos generales --}}
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">
                                    <i class="ri-file-list-3-line me-2 text-primary"></i>Datos de la Reserva
                                </h5>
                                <div class="d-flex gap-2">
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
                                    <span class="badge {{ $estadoClass }} px-3 py-2 fs-6">
                                        {{ ucfirst($reserva->estado_reserva) }}
                                    </span>
                                    <a href="{{ route('admin.reservas.edit', $reserva) }}" class="btn btn-sm btn-warning">
                                        <i class="ri-pencil-line me-1"></i>Editar
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <p class="text-muted mb-1 small">Código de Reserva</p>
                                        <p class="fw-bold fs-5 mb-0 text-primary">{{ $reserva->codigo_reserva }}</p>
                                    </div>

                                    <div class="col-md-4">
                                        <p class="text-muted mb-1 small">Fecha de Reserva</p>
                                        <p class="fw-semibold mb-0">{{ $reserva->fecha_reserva->format('d/m/Y H:i') }}</p>
                                    </div>

                                    <div class="col-md-4">
                                        <p class="text-muted mb-1 small">Fuente</p>
                                        <p class="fw-semibold mb-0">{{ $reserva->fuente_reserva ?? '—' }}</p>
                                    </div>

                                    <div class="col-md-4">
                                        <p class="text-muted mb-1 small">Tipo de Reserva</p>
                                        <p class="fw-semibold mb-0">{{ $reserva->tipo_reserva }}</p>
                                    </div>

                                    <div class="col-md-8">
                                        <p class="text-muted mb-1 small">Descripción del Servicio</p>
                                        <p class="fw-semibold mb-0">{{ $reserva->descripcion_servicio ?? '—' }}</p>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Card: Tour asociado --}}
                        @if($reserva->tour)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-compass-line me-2 text-success"></i>Tour Reservado
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-6">
                                            <p class="text-muted mb-1 small">Tour</p>
                                            <p class="fw-bold mb-0">
                                                <a href="{{ route('admin.tours.show', $reserva->tour) }}">
                                                    {{ $reserva->tour->nombre_tour }}
                                                </a>
                                            </p>
                                            <small class="text-muted">{{ $reserva->tour->codigo_tour }}</small>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="text-muted mb-1 small">Duración</p>
                                            <p class="fw-semibold mb-0">
                                                {{ $reserva->tour->duracion_dias ?? '—' }} días
                                                @if($reserva->tour->duracion_noches)
                                                    / {{ $reserva->tour->duracion_noches }} noches
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-3 text-end">
                                            <a href="{{ route('admin.tours.reservas.calendario', ['tour' => $reserva->tour, 'anio' => $reserva->fecha_inicio->year]) }}"
                                               class="btn btn-sm btn-outline-success">
                                                <i class="ri-calendar-2-line me-1"></i>Ver en Calendario
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Card: Fechas y pasajeros --}}
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-calendar-event-line me-2 text-info"></i>Fechas y Pasajeros
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <div class="col-md-3">
                                        <p class="text-muted mb-1 small">Fecha de Inicio</p>
                                        <p class="fw-bold mb-0 text-success">{{ $reserva->fecha_inicio->format('d/m/Y') }}</p>
                                        <small class="text-muted">{{ $reserva->fecha_inicio->isoFormat('dddd') }}</small>
                                    </div>

                                    <div class="col-md-3">
                                        <p class="text-muted mb-1 small">Fecha de Fin</p>
                                        @if($reserva->fecha_fin)
                                            <p class="fw-bold mb-0 text-danger">{{ $reserva->fecha_fin->format('d/m/Y') }}</p>
                                            <small class="text-muted">{{ $reserva->fecha_fin->isoFormat('dddd') }}</small>
                                        @elseif($reserva->tour && $reserva->tour->duracion_dias)
                                            @php $calculada = $reserva->fecha_inicio->copy()->addDays($reserva->tour->duracion_dias - 1) @endphp
                                            <p class="fw-bold mb-0 text-warning">{{ $calculada->format('d/m/Y') }}</p>
                                            <small class="text-muted">calculada por duración del tour</small>
                                        @else
                                            <p class="fw-semibold mb-0 text-muted">—</p>
                                        @endif
                                    </div>

                                    <div class="col-md-6">
                                        <p class="text-muted mb-1 small">Pasajeros</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                                <i class="ri-group-line me-1"></i>Total: {{ $reserva->num_pasajeros }}
                                            </span>
                                            @if($reserva->num_adultos)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-2">
                                                    Adultos: {{ $reserva->num_adultos }}
                                                </span>
                                            @endif
                                            @if($reserva->num_ninos)
                                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-2">
                                                    Niños: {{ $reserva->num_ninos }}
                                                </span>
                                            @endif
                                            @if($reserva->num_bebes)
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-2">
                                                    Bebés: {{ $reserva->num_bebes }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        {{-- Card: Notas --}}
                        @if($reserva->notas || $reserva->requisitos_especiales)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-sticky-note-line me-2 text-warning"></i>Notas y Requisitos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    @if($reserva->notas)
                                        <p class="text-muted mb-1 small fw-semibold">Notas / Observaciones</p>
                                        <p class="mb-3">{{ $reserva->notas }}</p>
                                    @endif
                                    @if($reserva->requisitos_especiales)
                                        <p class="text-muted mb-1 small fw-semibold">Requisitos Especiales</p>
                                        <p class="mb-0">{{ $reserva->requisitos_especiales }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                    </div>

                    {{-- ── Columna derecha: cliente, agente, finanzas ── --}}
                    <div class="col-xl-4">

                        {{-- Card: Cliente --}}
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-user-line me-2 text-primary"></i>Cliente
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($reserva->cliente)
                                    <p class="fw-bold mb-1">
                                        <a href="{{ route('admin.clientes.show', $reserva->cliente) }}">
                                            {{ $reserva->cliente->nombre_completo }}
                                        </a>
                                    </p>
                                    <p class="text-muted mb-1 small">
                                        <i class="ri-id-card-line me-1"></i>
                                        {{ strtoupper($reserva->cliente->tipo_documento ?? '') }}:
                                        {{ $reserva->cliente->numero_documento ?? '—' }}
                                    </p>
                                    @if($reserva->cliente->email)
                                        <p class="text-muted mb-1 small">
                                            <i class="ri-mail-line me-1"></i>{{ $reserva->cliente->email }}
                                        </p>
                                    @endif
                                    @if($reserva->cliente->telefono)
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-phone-line me-1"></i>{{ $reserva->cliente->telefono }}
                                        </p>
                                    @endif
                                @else
                                    <p class="text-muted mb-0">Sin cliente asignado</p>
                                @endif
                            </div>
                        </div>

                        {{-- Card: Agente --}}
                        @if($reserva->agente)
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="ri-customer-service-2-line me-2 text-info"></i>Agente de Ventas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <p class="fw-bold mb-1">
                                        <a href="{{ route('admin.agentes.show', $reserva->agente) }}">
                                            {{ $reserva->agente->nombres . ' ' . $reserva->agente->apellidos }}
                                        </a>
                                    </p>
                                    <p class="text-muted mb-1 small">
                                        <i class="ri-price-tag-3-line me-1"></i>Código: {{ $reserva->agente->codigo_agente }}
                                    </p>
                                    @if($reserva->agente->email)
                                        <p class="text-muted mb-0 small">
                                            <i class="ri-mail-line me-1"></i>{{ $reserva->agente->email }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- Card: Resumen financiero --}}
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-money-dollar-circle-line me-2 text-success"></i>Resumen Financiero
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-sm mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted ps-3">Moneda</td>
                                            <td class="fw-semibold text-end pe-3">{{ $reserva->moneda }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3">Precio Total</td>
                                            <td class="fw-semibold text-end pe-3">{{ number_format($reserva->precio_total, 2) }}</td>
                                        </tr>
                                        @if($reserva->descuento > 0)
                                            <tr>
                                                <td class="text-muted ps-3">Descuento</td>
                                                <td class="fw-semibold text-danger text-end pe-3">- {{ number_format($reserva->descuento, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-light">
                                            <td class="fw-bold ps-3">Precio Final</td>
                                            <td class="fw-bold text-success text-end pe-3 fs-6">{{ number_format($reserva->precio_final, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3">Monto Pagado</td>
                                            <td class="fw-semibold text-end pe-3">{{ number_format($reserva->monto_pagado, 2) }}</td>
                                        </tr>
                                        <tr class="{{ $reserva->saldo_pendiente > 0 ? 'table-warning' : 'table-success' }}">
                                            <td class="fw-bold ps-3">Saldo Pendiente</td>
                                            <td class="fw-bold text-end pe-3">{{ number_format($reserva->saldo_pendiente, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if($reserva->precio_final > 0)
                                <div class="card-footer py-2">
                                    @php $pct = min(100, round(($reserva->monto_pagado / $reserva->precio_final) * 100)) @endphp
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Progreso de pago</small>
                                        <small class="fw-semibold">{{ $pct }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-info' : 'bg-warning') }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Pagos de la reserva --}}
                        <div class="card mt-3">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">
                                    <i class="ri-bank-card-line me-2 text-success"></i>Pagos
                                    <span class="badge bg-secondary ms-1">{{ $reserva->pagos->count() }}</span>
                                </h5>
                                <a href="{{ route('admin.pagos.create', ['reserva_id' => $reserva->id]) }}"
                                   class="btn btn-sm btn-success">
                                    <i class="ri-add-line"></i> Agregar
                                </a>
                            </div>
                            @if($reserva->pagos->isNotEmpty())
                                <div class="card-body p-0">
                                    <table class="table table-sm mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Código</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Método</th>
                                            <th>Estado</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($reserva->pagos as $pago)
                                            <tr>
                                                <td class="ps-3">
                                                    <a href="{{ route('admin.pagos.show', $pago) }}" class="small fw-semibold">
                                                        {{ $pago->codigo_pago }}
                                                    </a>
                                                </td>
                                                <td><small>{{ $pago->fecha_pago->format('d/m/Y') }}</small></td>
                                                <td>
                                                    <strong class="{{ $pago->tipo_pago === 'devolucion' ? 'text-danger' : 'text-success' }}">
                                                        {{ $pago->moneda }} {{ number_format($pago->monto, 2) }}
                                                    </strong>
                                                </td>
                                                <td><small>{{ $pago->metodo_texto }}</small></td>
                                                <td>
                                                    @php $sc = match($pago->estado) { 'confirmado'=>'bg-success','pendiente'=>'bg-warning','rechazado'=>'bg-danger', default=>'bg-secondary' }; @endphp
                                                    <span class="badge {{ $sc }}">{{ ucfirst($pago->estado) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="card-body py-3 text-center text-muted">
                                    <small>Sin pagos registrados</small>
                                </div>
                            @endif
                        </div>

                        {{-- Acciones rápidas --}}
                        <div class="card mt-3">
                            <div class="card-body d-grid gap-2">
                                <a href="{{ route('admin.reservas.edit', $reserva) }}" class="btn btn-warning">
                                    <i class="ri-pencil-line me-1"></i>Editar Reserva
                                </a>
                                @if($reserva->tour)
                                    <a href="{{ route('admin.tours.reservas.calendario', ['tour' => $reserva->tour, 'anio' => $reserva->fecha_inicio->year]) }}"
                                       class="btn btn-outline-success">
                                        <i class="ri-calendar-2-line me-1"></i>Ver en Calendario del Tour
                                    </a>
                                @endif
                                <a href="{{ route('admin.reservas.index') }}" class="btn btn-outline-secondary">
                                    <i class="ri-list-check me-1"></i>Volver a Reservas
                                </a>
                                @if($reserva->estado_reserva !== 'pagada' && $reserva->monto_pagado == 0)
                                    <form action="{{ route('admin.reservas.destroy', $reserva) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar esta reserva?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="ri-delete-bin-line me-1"></i>Eliminar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>{{-- /row --}}

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
