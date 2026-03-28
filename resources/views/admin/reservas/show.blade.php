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

                        @php
                            $pasajerosAdultos = $reserva->pasajeros->where('tipo_pasajero', 'adulto')->count();
                            $pasajerosEstudiantes = $reserva->pasajeros->where('tipo_pasajero', 'estudiante')->count();
                            $pasajerosNinos = $reserva->pasajeros->where('tipo_pasajero', 'nino')->count();
                        @endphp
                        <div class="card mt-3">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">
                                    <i class="ri-user-add-line me-2 text-primary"></i>Gestión de Pasajeros
                                </h5>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarPasajero">
                                    <i class="ri-add-line me-1"></i>Agregar pasajero
                                </button>
                            </div>
                            <div class="card-body">
                                @if(!$reserva->tour_id)
                                    <div class="alert alert-warning mb-0">
                                        Esta reserva no tiene tour asociado. No se pueden registrar pasajeros hasta vincular un tour.
                                    </div>
                                @else
                                    <div id="pasajerosReservaAlert" class="alert d-none"></div>

                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                                            Registrados: <span id="reservaPasajerosRegistrados">{{ $reserva->pasajeros->count() }}</span>
                                        </span>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                            Adultos: <span id="reservaPasajerosAdultos">{{ $pasajerosAdultos }}</span>
                                        </span>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2">
                                            Estudiantes: <span id="reservaPasajerosEstudiantes">{{ $pasajerosEstudiantes }}</span>
                                        </span>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2">
                                            Niños: <span id="reservaPasajerosNinos">{{ $pasajerosNinos }}</span>
                                        </span>
                                    </div>

                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad de adultos</label>
                                            <select id="cantidadAdultos" class="form-select">
                                                @for($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad de estudiantes</label>
                                            <select id="cantidadEstudiantes" class="form-select">
                                                @for($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Cantidad de niños</label>
                                            <select id="cantidadNinos" class="form-select">
                                                @for($i = 0; $i <= 10; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-grid">
                                            <button type="button" class="btn btn-outline-primary" id="btnGenerarPasajeros">
                                                <i class="ri-layout-grid-line me-1"></i>Generar formularios
                                            </button>
                                        </div>
                                    </div>

                                    <div id="bulkPasajerosBox" class="mt-4 d-none">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0">Pasajeros adicionales</h6>
                                            <button type="button" class="btn btn-success btn-sm" id="btnGuardarPasajerosBulk">
                                                <span class="spinner-border spinner-border-sm d-none me-1" id="spinnerPasajerosBulk"></span>
                                                <i class="ri-save-line me-1"></i>Guardar todos
                                            </button>
                                        </div>
                                        <div id="bulkPasajerosContainer" class="row g-3"></div>
                                    </div>
                                @endif
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
                                                <i class="ri-group-line me-1"></i>Total: <span id="reservaResumenTotal">{{ $reserva->num_pasajeros }}</span>
                                            </span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-2">
                                                Adultos: <span id="reservaResumenAdultos">{{ $reserva->num_adultos }}</span>
                                            </span>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-2">
                                                Niños: <span id="reservaResumenNinos">{{ $reserva->num_ninos }}</span>
                                            </span>
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

                        <div class="card mt-3">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <h5 class="card-title mb-0">
                                    <i class="ri-team-line me-2 text-primary"></i>Pasajeros asignados a la reserva
                                </h5>
                                <span class="badge bg-secondary" id="tablaPasajerosBadge">{{ $reserva->pasajeros->count() }}</span>
                            </div>
                            <div class="card-body">
                                <div id="tablaPasajerosAlert" class="alert d-none"></div>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Tipo</th>
                                            <th>Pasajero</th>
                                            <th>Documento</th>
                                            <th>Contacto</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tablaPasajerosReservaBody">
                                        @forelse($reserva->pasajeros as $pasajero)
                                            @php
                                                $tipoLabel = match($pasajero->tipo_pasajero) {
                                                    'estudiante' => 'Estudiante',
                                                    'nino' => 'Nino',
                                                    default => 'Adulto',
                                                };
                                            @endphp
                                            <tr data-pasajero-id="{{ $pasajero->id }}">
                                                <td>
                                                    <span class="badge bg-light text-dark border">{{ $tipoLabel }}</span>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">
                                                        <a href="{{ route('admin.pasajeros.show', $pasajero) }}">{{ $pasajero->nombre_completo }}</a>
                                                    </div>
                                                    <div class="text-muted small">
                                                        {{ $pasajero->genero ? ucfirst($pasajero->genero) : 'Sin genero' }}
                                                        @if($pasajero->fecha_nacimiento)
                                                            <span class="ms-1">| {{ $pasajero->fecha_nacimiento->format('d/m/Y') }}</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ strtoupper($pasajero->tipo_documento ?? '-') }}</div>
                                                    <div class="text-muted small">{{ $pasajero->numero_documento }}</div>
                                                </td>
                                                <td>
                                                    <div class="small">{{ $pasajero->email ?: 'Sin email' }}</div>
                                                    <div class="text-muted small">{{ $pasajero->telefono ?: ($pasajero->whatsapp ?: 'Sin telefono') }}</div>
                                                </td>
                                                <td class="text-end">
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-warning btn-editar-pasajero"
                                                        data-id="{{ $pasajero->id }}">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                    <button
                                                        type="button"
                                                        class="btn btn-sm btn-outline-danger btn-eliminar-pasajero"
                                                        data-id="{{ $pasajero->id }}"
                                                        data-nombre="{{ $pasajero->nombre_completo }}">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr id="tablaPasajerosEmpty">
                                                <td colspan="5" class="text-center text-muted py-4">Sin pasajeros vinculados a esta reserva.</td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if($reserva->addons->isNotEmpty())
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title mb-0"><i class="ri-service-line me-2 text-info"></i>Addons contratados</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">Addon</th>
                                                <th>Cantidad</th>
                                                <th>Monto Unitario</th>
                                                <th class="text-end pe-3">Total</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($reserva->addons as $addon)
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="fw-semibold">{{ $addon->nombre }}</div>
                                                        <small class="text-muted">{{ $addon->descripcion ?: 'Sin descripción' }}</small>
                                                    </td>
                                                    <td>{{ $addon->pivot->cantidad }}</td>
                                                    <td>USD {{ number_format($addon->pivot->monto_unitario, 2) }}</td>
                                                    <td class="text-end pe-3">USD {{ number_format($addon->pivot->monto_total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

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

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-group-line me-2 text-info"></i>Pasajeros
                                    <span class="badge bg-secondary ms-1" id="pasajerosListaBadge">{{ $reserva->pasajeros->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body" id="listaPasajerosReserva">
                                @forelse($reserva->pasajeros as $pasajero)
                                    @php
                                        $tipoLabel = match($pasajero->tipo_pasajero) {
                                            'estudiante' => 'Estudiante',
                                            'nino' => 'Nino',
                                            default => 'Adulto',
                                        };
                                    @endphp
                                    <div class="d-flex justify-content-between border-bottom py-2" data-pasajero-id="{{ $pasajero->id }}">
                                        <div>
                                            <a href="{{ route('admin.pasajeros.show', $pasajero) }}" class="fw-semibold">{{ $pasajero->nombre_completo }}</a>
                                            <div class="text-muted small">
                                                {{ $pasajero->numero_documento }}
                                                <span class="badge bg-light text-dark border ms-1">{{ $tipoLabel }}</span>
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.tours.show', $pasajero->tour) }}" class="small">{{ $pasajero->tour?->nombre_tour ?? 'Sin tour' }}</a>
                                    </div>
                                @empty
                                    <small class="text-muted">Sin pasajeros vinculados.</small>
                                @endforelse
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
                        @php
                            $cantidadPersonasFinanciera = max(1, (int) $reserva->num_pasajeros);
                            $tarifaPorPersona = (float) $reserva->precio_total;
                            $subtotalReserva = $tarifaPorPersona * $cantidadPersonasFinanciera;
                            $addonsReservaTotal = (float) $reserva->addons->sum(fn ($addon) => $addon->pivot->monto_total);
                            $descuentoReserva = (float) ($reserva->descuento ?? 0);
                            $totalReserva = max(0, $subtotalReserva + $addonsReservaTotal - $descuentoReserva);
                            $pagosRealizados = $reserva->pagos
                                ->whereNotIn('estado', ['rechazado'])
                                ->whereNotIn('tipo_pago', ['proveedor', 'devolucion']);
                            $cantidadPagosRealizados = $pagosRealizados->count();
                            $montoTotalPagado = (float) $pagosRealizados->sum('monto');
                            $saldoPorPagar = max(0, $totalReserva - $montoTotalPagado);
                            $pctPago = $totalReserva > 0 ? min(100, round(($montoTotalPagado / $totalReserva) * 100)) : 0;
                        @endphp
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
                                            <td class="text-muted ps-3">Tarifa por persona</td>
                                            <td class="fw-semibold text-end pe-3">{{ number_format($tarifaPorPersona, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3">Cantidad de personas</td>
                                            <td class="fw-semibold text-end pe-3">{{ $cantidadPersonasFinanciera }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3">Subtotal de la reserva</td>
                                            <td class="fw-semibold text-end pe-3">{{ number_format($subtotalReserva, 2) }}</td>
                                        </tr>
                                        @if($addonsReservaTotal > 0)
                                            <tr>
                                                <td class="text-muted ps-3">Addons</td>
                                                <td class="fw-semibold text-end pe-3">{{ number_format($addonsReservaTotal, 2) }}</td>
                                            </tr>
                                        @endif
                                        @if($reserva->descuento > 0)
                                            <tr>
                                                <td class="text-muted ps-3">Descuento</td>
                                                <td class="fw-semibold text-danger text-end pe-3">- {{ number_format($descuentoReserva, 2) }}</td>
                                            </tr>
                                        @endif
                                        <tr class="table-light">
                                            <td class="fw-bold ps-3">Total a pagar</td>
                                            <td class="fw-bold text-success text-end pe-3 fs-6">{{ number_format($totalReserva, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3">Pagos realizados</td>
                                            <td class="fw-semibold text-end pe-3">{{ $cantidadPagosRealizados }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted ps-3">Monto total pagado</td>
                                            <td class="fw-semibold text-end pe-3">{{ number_format($montoTotalPagado, 2) }}</td>
                                        </tr>
                                        <tr class="{{ $saldoPorPagar > 0 ? 'table-warning' : 'table-success' }}">
                                            <td class="fw-bold ps-3">Saldo por pagar</td>
                                            <td class="fw-bold text-end pe-3">{{ number_format($saldoPorPagar, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @if($totalReserva > 0)
                                <div class="card-footer py-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="text-muted">Progreso de pago</small>
                                        <small class="fw-semibold">{{ $pctPago }}%</small>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar {{ $pctPago >= 100 ? 'bg-success' : ($pctPago >= 50 ? 'bg-info' : 'bg-warning') }}"
                                             style="width: {{ $pctPago }}%"></div>
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

@if($reserva->tour_id)
<div class="modal fade" id="modalAgregarPasajero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="ri-user-add-line me-1"></i>Agregar pasajero a la reserva</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalPasajeroAlert" class="alert d-none"></div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select id="modal_tipo_pasajero" class="form-select">
                            <option value="adulto">Adulto</option>
                            <option value="estudiante">Estudiante</option>
                            <option value="nino">Niño</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="modal_nombre" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Apellido</label>
                        <input type="text" id="modal_apellido" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo documento</label>
                        <select id="modal_tipo_documento" class="form-select">
                            <option value="passport">Passport</option>
                            <option value="dni">DNI</option>
                            <option value="id">ID</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Número documento</label>
                        <input type="text" id="modal_numero_documento" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Género</label>
                        <select id="modal_genero" class="form-select">
                            <option value="">Seleccionar...</option>
                            <option value="male">Masculino</option>
                            <option value="female">Femenino</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha nacimiento</label>
                        <input type="text" id="modal_fecha_nacimiento" class="form-control flatpickr-date" data-date-format="Y-m-d">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" id="modal_email" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Teléfono</label>
                        <input type="text" id="modal_telefono" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPasajeroModal">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="spinnerPasajeroModal"></span>
                    <i class="ri-save-line me-1"></i>Guardar pasajero
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarPasajero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="ri-pencil-line me-1"></i>Editar pasajero</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalEditarPasajeroAlert" class="alert d-none"></div>
                <input type="hidden" id="edit_pasajero_id">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select id="edit_tipo_pasajero" class="form-select">
                            <option value="adulto">Adulto</option>
                            <option value="estudiante">Estudiante</option>
                            <option value="nino">Nino</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" id="edit_nombre" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Apellido</label>
                        <input type="text" id="edit_apellido" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tipo documento</label>
                        <select id="edit_tipo_documento" class="form-select">
                            <option value="passport">Passport</option>
                            <option value="dni">DNI</option>
                            <option value="id">ID</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Numero documento</label>
                        <input type="text" id="edit_numero_documento" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Genero</label>
                        <select id="edit_genero" class="form-select">
                            <option value="">Seleccionar...</option>
                            <option value="male">Masculino</option>
                            <option value="female">Femenino</option>
                            <option value="other">Otro</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha nacimiento</label>
                        <input type="text" id="edit_fecha_nacimiento" class="form-control flatpickr-date" data-date-format="Y-m-d">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" id="edit_email" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Telefono</label>
                        <input type="text" id="edit_telefono" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnActualizarPasajeroModal">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="spinnerEditarPasajeroModal"></span>
                    <i class="ri-save-line me-1"></i>Guardar cambios
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEliminarPasajero" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="ri-delete-bin-line me-1"></i>Eliminar pasajero</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="modalEliminarPasajeroAlert" class="alert d-none"></div>
                <input type="hidden" id="delete_pasajero_id">
                <p class="mb-0">Se eliminara a <strong id="delete_pasajero_nombre">este pasajero</strong> de la reserva actual.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnEliminarPasajeroModal">
                    <span class="spinner-border spinner-border-sm d-none me-1" id="spinnerEliminarPasajeroModal"></span>
                    <i class="ri-delete-bin-line me-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const reservaId = {{ $reserva->id }};
    const pasajeroStoreUrl = '{{ route('admin.reservas.pasajeros.store-ajax', $reserva) }}';
    const pasajerosBulkUrl = '{{ route('admin.reservas.pasajeros.bulk-store-ajax', $reserva) }}';
    const pasajeroEditUrlTemplate = '{{ route('admin.reservas.pasajeros.edit-ajax', ['reserva' => $reserva, 'pasajero' => '__PASAJERO__']) }}';
    const pasajeroUpdateUrlTemplate = '{{ route('admin.reservas.pasajeros.update-ajax', ['reserva' => $reserva, 'pasajero' => '__PASAJERO__']) }}';
    const pasajeroDestroyUrlTemplate = '{{ route('admin.reservas.pasajeros.destroy-ajax', ['reserva' => $reserva, 'pasajero' => '__PASAJERO__']) }}';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '{{ csrf_token() }}';

    const alertBox = document.getElementById('pasajerosReservaAlert');
    const bulkBox = document.getElementById('bulkPasajerosBox');
    const bulkContainer = document.getElementById('bulkPasajerosContainer');
    const tableAlert = document.getElementById('tablaPasajerosAlert');
    const tableBody = document.getElementById('tablaPasajerosReservaBody');
    const sideList = document.getElementById('listaPasajerosReserva');

    function buildUrl(template, pasajeroId) {
        return template.replace('__PASAJERO__', pasajeroId);
    }

    function mostrarAlerta(message, type = 'success', target = alertBox) {
        if (!target) return;
        target.className = `alert alert-${type}`;
        target.textContent = message;
        target.classList.remove('d-none');
        setTimeout(() => target.classList.add('d-none'), 4000);
    }

    function actualizarContadoresDesdeResumen(resumen) {
        if (!resumen) return;

        const rows = tableBody ? Array.from(tableBody.querySelectorAll('tr[data-pasajero-id]')) : [];
        const totalRows = rows.length;
        const adultos = rows.filter((row) => row.textContent.includes('Adulto')).length;
        const estudiantes = rows.filter((row) => row.textContent.includes('Estudiante')).length;
        const ninos = rows.filter((row) => row.textContent.includes('Nino')).length;

        document.getElementById('tablaPasajerosBadge').textContent = totalRows;
        document.getElementById('pasajerosListaBadge').textContent = totalRows;
        document.getElementById('reservaPasajerosRegistrados').textContent = totalRows;
        document.getElementById('reservaPasajerosAdultos').textContent = adultos;
        document.getElementById('reservaPasajerosEstudiantes').textContent = estudiantes;
        document.getElementById('reservaPasajerosNinos').textContent = ninos;
        document.getElementById('reservaResumenTotal').textContent = resumen.num_pasajeros ?? totalRows;
        document.getElementById('reservaResumenAdultos').textContent = resumen.num_adultos ?? 0;
        document.getElementById('reservaResumenNinos').textContent = resumen.num_ninos ?? 0;
    }

    function badgeTipo(tipo) {
        if (tipo === 'estudiante') return 'Estudiante';
        if (tipo === 'nino') return 'Niño';
        return 'Adulto';
    }

    function agregarPasajeroAlListado(pasajero) {
        const cardBody = document.getElementById('listaPasajerosReserva');

        if (!cardBody) return;

        const empty = cardBody.querySelector('small.text-muted');
        if (empty && empty.textContent.includes('Sin pasajeros')) {
            empty.remove();
        }

        const row = document.createElement('div');
        row.className = 'd-flex justify-content-between border-bottom py-2';
        row.innerHTML = `
            <div>
                <a href="${pasajero.show_url}" class="fw-semibold">${pasajero.nombre_completo}</a>
                <div class="text-muted small">
                    ${pasajero.numero_documento}
                    <span class="badge bg-light text-dark border ms-1">${badgeTipo(pasajero.tipo_pasajero)}</span>
                </div>
            </div>
            <span class="small">${pasajero.tour_nombre ?? ''}</span>
        `;
        cardBody.prepend(row);
    }

    function generoLabel(genero) {
        if (genero === 'male') return 'Masculino';
        if (genero === 'female') return 'Femenino';
        if (genero === 'other') return 'Otro';
        return 'Sin genero';
    }

    function contactoLabel(pasajero) {
        return pasajero.telefono || pasajero.whatsapp || 'Sin telefono';
    }

    function badgeTipo(tipo) {
        if (tipo === 'estudiante') return 'Estudiante';
        if (tipo === 'nino') return 'Nino';
        return 'Adulto';
    }

    function renderPassengerSideItem(pasajero) {
        return `
            <div class="d-flex justify-content-between border-bottom py-2" data-pasajero-id="${pasajero.id}">
                <div>
                    <a href="${pasajero.show_url}" class="fw-semibold">${pasajero.nombre_completo}</a>
                    <div class="text-muted small">
                        ${pasajero.numero_documento}
                        <span class="badge bg-light text-dark border ms-1">${badgeTipo(pasajero.tipo_pasajero)}</span>
                    </div>
                </div>
                <span class="small">${pasajero.tour_nombre ?? 'Sin tour'}</span>
            </div>
        `;
    }

    function renderPassengerTableRow(pasajero) {
        const fechaNacimiento = pasajero.fecha_nacimiento
            ? new Date(`${pasajero.fecha_nacimiento}T00:00:00`).toLocaleDateString('es-PE')
            : '';

        return `
            <tr data-pasajero-id="${pasajero.id}">
                <td><span class="badge bg-light text-dark border">${badgeTipo(pasajero.tipo_pasajero)}</span></td>
                <td>
                    <div class="fw-semibold"><a href="${pasajero.show_url}">${pasajero.nombre_completo}</a></div>
                    <div class="text-muted small">
                        ${generoLabel(pasajero.genero)}
                        ${fechaNacimiento ? `<span class="ms-1">| ${fechaNacimiento}</span>` : ''}
                    </div>
                </td>
                <td>
                    <div class="fw-medium">${(pasajero.tipo_documento || '-').toUpperCase()}</div>
                    <div class="text-muted small">${pasajero.numero_documento}</div>
                </td>
                <td>
                    <div class="small">${pasajero.email || 'Sin email'}</div>
                    <div class="text-muted small">${contactoLabel(pasajero)}</div>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-warning btn-editar-pasajero" data-id="${pasajero.id}">
                        <i class="ri-pencil-line"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-eliminar-pasajero" data-id="${pasajero.id}" data-nombre="${pasajero.nombre_completo}">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </td>
            </tr>
        `;
    }

    function syncTableEmptyState() {
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll('tr[data-pasajero-id]');
        const emptyRow = document.getElementById('tablaPasajerosEmpty');

        if (!rows.length) {
            if (!emptyRow) {
                tableBody.innerHTML = '<tr id="tablaPasajerosEmpty"><td colspan="5" class="text-center text-muted py-4">Sin pasajeros vinculados a esta reserva.</td></tr>';
            }
        } else if (emptyRow) {
            emptyRow.remove();
        }
    }

    function syncSideEmptyState() {
        if (!sideList) return;

        const items = sideList.querySelectorAll('[data-pasajero-id]');
        const empty = sideList.querySelector('small.text-muted');

        if (!items.length) {
            if (!empty) {
                sideList.innerHTML = '<small class="text-muted">Sin pasajeros vinculados.</small>';
            }
        } else if (empty) {
            empty.remove();
        }
    }

    function upsertPassengerSideItem(pasajero, prepend = false) {
        if (!sideList) return;

        syncSideEmptyState();
        const existing = sideList.querySelector(`[data-pasajero-id="${pasajero.id}"]`);
        if (existing) {
            existing.outerHTML = renderPassengerSideItem(pasajero);
            return;
        }

        sideList.insertAdjacentHTML(prepend ? 'afterbegin' : 'beforeend', renderPassengerSideItem(pasajero));
    }

    function upsertPassengerTableRow(pasajero, prepend = false) {
        if (!tableBody) return;

        syncTableEmptyState();
        const existing = tableBody.querySelector(`tr[data-pasajero-id="${pasajero.id}"]`);
        if (existing) {
            existing.outerHTML = renderPassengerTableRow(pasajero);
            return;
        }

        tableBody.insertAdjacentHTML(prepend ? 'afterbegin' : 'beforeend', renderPassengerTableRow(pasajero));
    }

    function removePassengerFromUI(pasajeroId) {
        tableBody?.querySelector(`tr[data-pasajero-id="${pasajeroId}"]`)?.remove();
        sideList?.querySelector(`[data-pasajero-id="${pasajeroId}"]`)?.remove();
        syncTableEmptyState();
        syncSideEmptyState();
    }

    function agregarPasajeroAlListado(pasajero) {
        upsertPassengerSideItem(pasajero, true);
        upsertPassengerTableRow(pasajero, true);
    }

    function limpiarModalPasajero() {
        ['modal_nombre', 'modal_apellido', 'modal_numero_documento', 'modal_email', 'modal_telefono'].forEach(function (id) {
            const input = document.getElementById(id);
            if (input) input.value = '';
        });
        document.getElementById('modal_tipo_pasajero').value = 'adulto';
        document.getElementById('modal_tipo_documento').value = 'passport';
        document.getElementById('modal_genero').value = '';
        if (window.setFlatpickrDate) {
            window.setFlatpickrDate(document.getElementById('modal_fecha_nacimiento'), '');
        } else {
            document.getElementById('modal_fecha_nacimiento').value = '';
        }
    }

    function limpiarModalEditarPasajero() {
        ['edit_pasajero_id', 'edit_nombre', 'edit_apellido', 'edit_numero_documento', 'edit_email', 'edit_telefono'].forEach(function (id) {
            const input = document.getElementById(id);
            if (input) input.value = '';
        });
        document.getElementById('edit_tipo_pasajero').value = 'adulto';
        document.getElementById('edit_tipo_documento').value = 'passport';
        document.getElementById('edit_genero').value = '';
        if (window.setFlatpickrDate) {
            window.setFlatpickrDate(document.getElementById('edit_fecha_nacimiento'), '');
        } else {
            document.getElementById('edit_fecha_nacimiento').value = '';
        }
    }

    function cargarPasajeroEnModal(pasajero) {
        document.getElementById('edit_pasajero_id').value = pasajero.id;
        document.getElementById('edit_tipo_pasajero').value = pasajero.tipo_pasajero || 'adulto';
        document.getElementById('edit_nombre').value = pasajero.nombre || '';
        document.getElementById('edit_apellido').value = pasajero.apellido || '';
        document.getElementById('edit_tipo_documento').value = pasajero.tipo_documento || 'passport';
        document.getElementById('edit_numero_documento').value = pasajero.numero_documento || '';
        document.getElementById('edit_genero').value = pasajero.genero || '';
        document.getElementById('edit_email').value = pasajero.email || '';
        document.getElementById('edit_telefono').value = pasajero.telefono || '';
        if (window.setFlatpickrDate) {
            window.setFlatpickrDate(document.getElementById('edit_fecha_nacimiento'), pasajero.fecha_nacimiento || '');
        } else {
            document.getElementById('edit_fecha_nacimiento').value = pasajero.fecha_nacimiento || '';
        }
    }

    function cardPasajero(index, tipo) {
        return `
            <div class="col-12">
                <div class="border rounded p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Pasajero ${index + 1}</h6>
                        <span class="badge bg-light text-dark border">${badgeTipo(tipo)}</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control" data-key="nombre">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Apellido</label>
                            <input type="text" class="form-control" data-key="apellido">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipo Doc.</label>
                            <select class="form-select" data-key="tipo_documento">
                                <option value="passport">Passport</option>
                                <option value="dni">DNI</option>
                                <option value="id">ID</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">N° Documento</label>
                            <input type="text" class="form-control" data-key="numero_documento">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Género</label>
                            <select class="form-select" data-key="genero">
                                <option value="">-</option>
                                <option value="male">Masculino</option>
                                <option value="female">Femenino</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Nacimiento</label>
                            <input type="text" class="form-control flatpickr-date" data-date-format="Y-m-d" data-key="fecha_nacimiento">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" data-key="email">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" data-key="telefono">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" class="form-control" data-key="whatsapp">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    document.getElementById('btnGenerarPasajeros')?.addEventListener('click', function () {
        const cantidades = [
            { tipo: 'adulto', cantidad: parseInt(document.getElementById('cantidadAdultos').value || '0', 10) },
            { tipo: 'estudiante', cantidad: parseInt(document.getElementById('cantidadEstudiantes').value || '0', 10) },
            { tipo: 'nino', cantidad: parseInt(document.getElementById('cantidadNinos').value || '0', 10) },
        ];

        let html = '';
        let index = 0;

        cantidades.forEach(function (grupo) {
            for (let i = 0; i < grupo.cantidad; i++) {
                html += `<div data-tipo="${grupo.tipo}">${cardPasajero(index, grupo.tipo)}</div>`;
                index++;
            }
        });

        bulkContainer.innerHTML = html;
        bulkBox.classList.toggle('d-none', index === 0);

        bulkContainer.querySelectorAll('.flatpickr-date').forEach(function (input) {
            if (window.flatpickr && !input._flatpickr) {
                window.flatpickr(input, {
                    altInput: true,
                    altFormat: 'd/m/Y',
                    dateFormat: 'Y-m-d',
                    allowInput: true,
                    locale: 'es',
                });
            }
        });
    });

    document.getElementById('btnGuardarPasajeroModal')?.addEventListener('click', async function () {
        const spinner = document.getElementById('spinnerPasajeroModal');
        const alert = document.getElementById('modalPasajeroAlert');
        spinner.classList.remove('d-none');
        this.disabled = true;
        alert.classList.add('d-none');

        const payload = {
            tipo_pasajero: document.getElementById('modal_tipo_pasajero').value,
            nombre: document.getElementById('modal_nombre').value.trim(),
            apellido: document.getElementById('modal_apellido').value.trim(),
            tipo_documento: document.getElementById('modal_tipo_documento').value,
            numero_documento: document.getElementById('modal_numero_documento').value.trim(),
            genero: document.getElementById('modal_genero').value || null,
            fecha_nacimiento: document.getElementById('modal_fecha_nacimiento').value || null,
            email: document.getElementById('modal_email').value.trim() || null,
            telefono: document.getElementById('modal_telefono').value.trim() || null,
        };

        try {
            const response = await fetch(pasajeroStoreUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudo guardar el pasajero.');
            }

            agregarPasajeroAlListado(data.pasajero);
            actualizarContadoresDesdeResumen(data.resumen);
            mostrarAlerta('Pasajero agregado correctamente.');
            bootstrap.Modal.getInstance(document.getElementById('modalAgregarPasajero')).hide();
            limpiarModalPasajero();
        } catch (error) {
            alert.className = 'alert alert-danger';
            alert.textContent = error.message;
            alert.classList.remove('d-none');
        } finally {
            spinner.classList.add('d-none');
            this.disabled = false;
        }
    });

    document.getElementById('modalAgregarPasajero')?.addEventListener('show.bs.modal', limpiarModalPasajero);
    document.getElementById('modalEditarPasajero')?.addEventListener('show.bs.modal', function () {
        document.getElementById('modalEditarPasajeroAlert')?.classList.add('d-none');
    });

    document.getElementById('btnGuardarPasajerosBulk')?.addEventListener('click', async function () {
        const cards = bulkContainer.querySelectorAll('[data-tipo]');
        const spinner = document.getElementById('spinnerPasajerosBulk');

        if (!cards.length) {
            mostrarAlerta('Primero genera los formularios de pasajeros.', 'warning');
            return;
        }

        const pasajeros = Array.from(cards).map(function (wrapper) {
            const root = wrapper;
            const data = { tipo_pasajero: root.dataset.tipo };
            root.querySelectorAll('[data-key]').forEach(function (input) {
                data[input.dataset.key] = input.value || null;
            });
            return data;
        });

        spinner.classList.remove('d-none');
        this.disabled = true;

        try {
            const response = await fetch(pasajerosBulkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ pasajeros }),
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudieron guardar los pasajeros.');
            }

            data.pasajeros.forEach(agregarPasajeroAlListado);
            actualizarContadoresDesdeResumen(data.resumen);
            bulkContainer.innerHTML = '';
            bulkBox.classList.add('d-none');
            document.getElementById('cantidadAdultos').value = '0';
            document.getElementById('cantidadEstudiantes').value = '0';
            document.getElementById('cantidadNinos').value = '0';
            mostrarAlerta(data.message || 'Pasajeros guardados correctamente.');
        } catch (error) {
            mostrarAlerta(error.message, 'danger');
        } finally {
            spinner.classList.add('d-none');
            this.disabled = false;
        }
    });

    document.addEventListener('click', async function (event) {
        const editButton = event.target.closest('.btn-editar-pasajero');
        if (editButton) {
            const pasajeroId = editButton.dataset.id;
            const alert = document.getElementById('modalEditarPasajeroAlert');
            limpiarModalEditarPasajero();
            alert.classList.add('d-none');

            try {
                const response = await fetch(buildUrl(pasajeroEditUrlTemplate, pasajeroId), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
                const data = await response.json();

                if (!response.ok || !data.ok) {
                    throw new Error(data.message || 'No se pudo cargar el pasajero.');
                }

                cargarPasajeroEnModal(data.pasajero);
                bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEditarPasajero')).show();
            } catch (error) {
                mostrarAlerta(error.message, 'danger', tableAlert);
            }

            return;
        }

        const deleteButton = event.target.closest('.btn-eliminar-pasajero');
        if (deleteButton) {
            document.getElementById('delete_pasajero_id').value = deleteButton.dataset.id;
            document.getElementById('delete_pasajero_nombre').textContent = deleteButton.dataset.nombre || 'este pasajero';
            document.getElementById('modalEliminarPasajeroAlert')?.classList.add('d-none');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('modalEliminarPasajero')).show();
        }
    });

    document.getElementById('btnActualizarPasajeroModal')?.addEventListener('click', async function () {
        const pasajeroId = document.getElementById('edit_pasajero_id').value;
        const spinner = document.getElementById('spinnerEditarPasajeroModal');
        const alert = document.getElementById('modalEditarPasajeroAlert');
        const payload = {
            tipo_pasajero: document.getElementById('edit_tipo_pasajero').value,
            nombre: document.getElementById('edit_nombre').value.trim(),
            apellido: document.getElementById('edit_apellido').value.trim(),
            tipo_documento: document.getElementById('edit_tipo_documento').value,
            numero_documento: document.getElementById('edit_numero_documento').value.trim(),
            genero: document.getElementById('edit_genero').value || null,
            fecha_nacimiento: document.getElementById('edit_fecha_nacimiento').value || null,
            email: document.getElementById('edit_email').value.trim() || null,
            telefono: document.getElementById('edit_telefono').value.trim() || null,
        };

        spinner.classList.remove('d-none');
        this.disabled = true;
        alert.classList.add('d-none');

        try {
            const response = await fetch(buildUrl(pasajeroUpdateUrlTemplate, pasajeroId), {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify(payload),
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudo actualizar el pasajero.');
            }

            upsertPassengerSideItem(data.pasajero);
            upsertPassengerTableRow(data.pasajero);
            actualizarContadoresDesdeResumen(data.resumen);
            bootstrap.Modal.getInstance(document.getElementById('modalEditarPasajero')).hide();
            mostrarAlerta(data.message || 'Pasajero actualizado correctamente.', 'success', tableAlert);
        } catch (error) {
            alert.className = 'alert alert-danger';
            alert.textContent = error.message;
            alert.classList.remove('d-none');
        } finally {
            spinner.classList.add('d-none');
            this.disabled = false;
        }
    });

    document.getElementById('btnEliminarPasajeroModal')?.addEventListener('click', async function () {
        const pasajeroId = document.getElementById('delete_pasajero_id').value;
        const spinner = document.getElementById('spinnerEliminarPasajeroModal');
        const alert = document.getElementById('modalEliminarPasajeroAlert');

        spinner.classList.remove('d-none');
        this.disabled = true;
        alert.classList.add('d-none');

        try {
            const response = await fetch(buildUrl(pasajeroDestroyUrlTemplate, pasajeroId), {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
            });
            const data = await response.json();

            if (!response.ok || !data.ok) {
                throw new Error(data.message || 'No se pudo eliminar el pasajero.');
            }

            removePassengerFromUI(pasajeroId);
            actualizarContadoresDesdeResumen(data.resumen);
            bootstrap.Modal.getInstance(document.getElementById('modalEliminarPasajero')).hide();
            mostrarAlerta(data.message || 'Pasajero eliminado correctamente.', 'success', tableAlert);
        } catch (error) {
            alert.className = 'alert alert-danger';
            alert.textContent = error.message;
            alert.classList.remove('d-none');
        } finally {
            spinner.classList.add('d-none');
            this.disabled = false;
        }
    });

    syncTableEmptyState();
    syncSideEmptyState();
    actualizarContadoresDesdeResumen({
        num_pasajeros: {{ $reserva->num_pasajeros }},
        num_adultos: {{ $reserva->num_adultos }},
        num_ninos: {{ $reserva->num_ninos }},
    });
});
</script>
@endif
<script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>
