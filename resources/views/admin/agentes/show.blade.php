@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Detalle Agente']); ?>
    @include('layouts.head-css')
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">
                                    {{ $agente->nombre_completo }}
                                    @php
                                        $estadoClases = [
                                            'activo'     => 'bg-success-subtle text-success border border-success-subtle',
                                            'inactivo'   => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                            'vacaciones' => 'bg-info-subtle text-info border border-info-subtle',
                                            'baja'       => 'bg-danger-subtle text-danger border border-danger-subtle',
                                        ];
                                    @endphp
                                    <span class="badge {{ $estadoClases[$agente->estado] ?? '' }} ms-2">
                                        {{ ucfirst($agente->estado) }}
                                    </span>
                                </h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.agentes.edit', $agente) }}" class="btn btn-warning btn-sm me-2">
                                        <i class="ri-pencil-line"></i> Editar
                                    </a>
                                    <a href="{{ route('admin.agentes.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="ri-arrow-left-line"></i> Volver
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show">
                                        <i class="ri-check-line me-1"></i>{{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="200">Código Agente</th>
                                            <td>{{ $agente->codigo_agente ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $agente->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Celular</th>
                                            <td>{{ $agente->celular }}</td>
                                        </tr>
                                        <tr>
                                            <th>Teléfono</th>
                                            <td>{{ $agente->telefono ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>DNI</th>
                                            <td>{{ $agente->dni ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha de Nacimiento</th>
                                            <td>{{ $agente->fecha_nacimiento ? $agente->fecha_nacimiento->format('d/m/Y') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Género</th>
                                            <td>{{ ucfirst($agente->genero ?? '—') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dirección</th>
                                            <td>{{ $agente->direccion ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Ciudad / País</th>
                                            <td>{{ $agente->ciudad }}, {{ $agente->pais }}</td>
                                        </tr>
                                        <tr>
                                            <th>Departamento</th>
                                            <td>{{ $agente->departamento ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Comisión</th>
                                            <td>{{ number_format($agente->comision_porcentaje, 2) }}%</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Ingreso</th>
                                            <td>{{ $agente->fecha_ingreso ? $agente->fecha_ingreso->format('d/m/Y') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fecha Salida</th>
                                            <td>{{ $agente->fecha_salida ? $agente->fecha_salida->format('d/m/Y') : '—' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Notas</th>
                                            <td>{{ $agente->notas ?? '—' }}</td>
                                        </tr>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Reservas del agente -->
                    <div class="col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="ri-calendar-check-line me-1"></i>
                                    Reservas ({{ $agente->reservas->count() }})
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                @forelse($agente->reservas->take(10) as $reserva)
                                    <div class="p-3 border-bottom">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('admin.reservas.show', $reserva) }}" class="fw-medium text-body">
                                                {{ $reserva->codigo_reserva }}
                                            </a>
                                            <span class="badge bg-{{ $reserva->estado_reserva == 'pagada' ? 'success' : ($reserva->estado_reserva == 'cancelada' ? 'danger' : 'warning') }}-subtle
                                                        text-{{ $reserva->estado_reserva == 'pagada' ? 'success' : ($reserva->estado_reserva == 'cancelada' ? 'danger' : 'warning') }}">
                                                {{ ucfirst($reserva->estado_reserva) }}
                                            </span>
                                        </div>
                                        <div class="text-muted small">{{ $reserva->fecha_inicio->format('d/m/Y') }}</div>
                                    </div>
                                @empty
                                    <div class="p-4 text-center text-muted">Sin reservas asignadas.</div>
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
</body>
</html>
