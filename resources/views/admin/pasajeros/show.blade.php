@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Detalle del Pasajero']); ?>
    @include('layouts.head-css')
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">Detalle del Pasajero</h4>
                        <div class="flex-shrink-0">
                            <a href="{{ route('admin.pasajeros.index') }}" class="btn btn-secondary btn-sm me-2">
                                <i class="ri-arrow-left-line"></i> Volver
                            </a>
                            <a href="{{ route('admin.pasajeros.edit', $pasajero) }}" class="btn btn-warning btn-sm">
                                <i class="ri-pencil-line"></i> Editar
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <h5 class="text-primary mb-3">Información Personal</h5>
                                <table class="table table-bordered">
                                    <tr><th width="220">Nombre Completo</th><td>{{ $pasajero->nombre_completo }}</td></tr>
                                    <tr><th>Documento</th><td>{{ strtoupper($pasajero->tipo_documento) }} - {{ $pasajero->numero_documento }}</td></tr>
                                    <tr><th>País</th><td>{{ $pasajero->pais?->nombre ?? '—' }}</td></tr>
                                    <tr><th>Idioma</th><td>{{ $pasajero->idioma?->nombre ?? '—' }}</td></tr>
                                    <tr><th>Dieta</th><td>{{ $pasajero->dieta?->nombre ?? '—' }}</td></tr>
                                    <tr><th>Fecha Nacimiento</th><td>{{ $pasajero->fecha_nacimiento?->format('d/m/Y') ?? '—' }}</td></tr>
                                    <tr><th>Pasaporte Expiración</th><td>{{ $pasajero->pasaporte_expiracion?->format('d/m/Y') ?? '—' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-lg-5">
                                <h5 class="text-primary mb-3">Relaciones</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="140">Cliente</th>
                                        <td>
                                            @if($pasajero->cliente)
                                                <a href="{{ route('admin.clientes.show', $pasajero->cliente) }}">{{ $pasajero->cliente->nombre_completo }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Reserva</th>
                                        <td>
                                            @if($pasajero->reserva)
                                                <a href="{{ route('admin.reservas.show', $pasajero->reserva) }}">{{ $pasajero->reserva->codigo_reserva }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tour</th>
                                        <td>
                                            @if($pasajero->tour)
                                                <a href="{{ route('admin.tours.show', $pasajero->tour) }}">{{ $pasajero->tour->nombre_tour }}</a>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Estado</th>
                                        <td>
                                            <span class="badge {{ $pasajero->activo ? 'bg-success' : 'bg-danger' }}">
                                                {{ $pasajero->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="row g-4 mt-1">
                            <div class="col-lg-6">
                                <h5 class="text-primary mb-3">Contacto</h5>
                                <table class="table table-bordered">
                                    <tr><th width="180">Email</th><td>{{ $pasajero->email ?? '—' }}</td></tr>
                                    <tr><th>Teléfono</th><td>{{ $pasajero->telefono ?? '—' }}</td></tr>
                                    <tr><th>WhatsApp</th><td>{{ $pasajero->whatsapp ?? '—' }}</td></tr>
                                </table>
                            </div>
                            <div class="col-lg-6">
                                <h5 class="text-primary mb-3">Emergencia y Salud</h5>
                                <table class="table table-bordered">
                                    <tr><th width="180">Contacto</th><td>{{ $pasajero->contacto_emergencia ?? '—' }}</td></tr>
                                    <tr><th>Teléfono</th><td>{{ $pasajero->telefono_emergencia ?? '—' }}</td></tr>
                                    <tr><th>Notas médicas</th><td>{{ $pasajero->notas_medicas ?? '—' }}</td></tr>
                                </table>
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
