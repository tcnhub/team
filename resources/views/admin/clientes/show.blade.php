@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Detalles del Cliente']); ?>

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
                                <h4 class="card-title mb-0 flex-grow-1">
                                    Detalles del Cliente
                                </h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary btn-sm me-2">
                                        <i class="ri-arrow-left-line"></i> Volver a la lista
                                    </a>
                                    <a href="{{ route('admin.clientes.edit', $cliente) }}" class="btn btn-warning btn-sm">
                                        <i class="ri-pencil-line"></i> Editar Cliente
                                    </a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <div class="row">
                                    <!-- Información Personal -->
                                    <div class="col-lg-8">
                                        <h5 class="mb-3 text-primary">
                                            <i class="ri-user-line"></i> Información Personal
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="180">Nombre Completo</th>
                                                    <td><strong>{{ $cliente->nombre_completo ?? $cliente->nombre . ' ' . $cliente->apellido }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th>Tipo de Documento</th>
                                                    <td>{{ strtoupper($cliente->tipo_documento) }}</td>
                                                </tr>
                                                <tr>
                                                    <th>Número de Documento</th>
                                                    <td><strong>{{ $cliente->numero_documento }}</strong></td>
                                                </tr>
                                                <tr>
                                                    <th>Género</th>
                                                    <td>
                                                        @if($cliente->genero)
                                                            {{ $cliente->genero == 'male' ? 'Masculino' : ($cliente->genero == 'female' ? 'Femenino' : 'Otro') }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Fecha de Nacimiento</th>
                                                    <td>
                                                        @if($cliente->fecha_nacimiento)
                                                            {{ $cliente->fecha_nacimiento->format('d/m/Y') }}
                                                            <small class="text-muted">({{ $cliente->edad ?? '—' }} años)</small>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>País</th>
                                                    <td>
                                                        @if($cliente->pais)
                                                            {{ $cliente->pais->nombre }}
                                                        @else
                                                            <span class="text-muted">No especificado</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Idioma Preferido</th>
                                                    <td>
                                                        @if($cliente->idioma)
                                                            {{ $cliente->idioma->nombre }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Dieta Preferida</th>
                                                    <td>
                                                        @if($cliente->dieta)
                                                            {{ $cliente->dieta->nombre }}
                                                        @else
                                                            Sin preferencia
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Estado y Contacto -->
                                    <div class="col-lg-4">
                                        <h5 class="mb-3 text-primary">
                                            <i class="ri-shield-check-line"></i> Estado
                                        </h5>
                                        <div class="mb-4">
                                            @php
                                                $estadoClass = $cliente->activo ? 'bg-success' : 'bg-danger';
                                            @endphp
                                            <span class="badge {{ $estadoClass }} px-4 py-2 fs-6">
                                                {{ $cliente->activo ? '✅ Activo' : '⛔ Inactivo' }}
                                            </span>
                                        </div>

                                        <h5 class="mb-3 text-primary">
                                            <i class="ri-phone-line"></i> Contacto
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <tr>
                                                    <th width="120">Email</th>
                                                    <td>
                                                        @if($cliente->email)
                                                            <a href="mailto:{{ $cliente->email }}">{{ $cliente->email }}</a>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Teléfono</th>
                                                    <td>{{ $cliente->telefono ?? '—' }}</td>
                                                </tr>
                                                <tr>
                                                    <th>WhatsApp</th>
                                                    <td>
                                                        @if($cliente->whatsapp)
                                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cliente->whatsapp) }}" target="_blank" class="text-success">
                                                                {{ $cliente->whatsapp }}
                                                            </a>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Información Adicional -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="mb-3 text-primary">
                                            <i class="ri-file-text-line"></i> Información Adicional
                                        </h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="border p-3 rounded">
                                                    <strong>Pasaporte Expiración:</strong><br>
                                                    @if($cliente->pasaporte_expiracion)
                                                        {{ $cliente->pasaporte_expiracion->format('d/m/Y') }}
                                                    @else
                                                        <span class="text-muted">No registrado</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="border p-3 rounded">
                                                    <strong>Contacto de Emergencia:</strong><br>
                                                    {{ $cliente->contacto_emergencia ?? '—' }}
                                                    @if($cliente->telefono_emergencia)
                                                        <br><small class="text-muted">Tel: {{ $cliente->telefono_emergencia }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if($cliente->notas_medicas)
                                            <div class="mt-4">
                                                <h6 class="text-primary">Notas Médicas / Requisitos Especiales</h6>
                                                <div class="border p-3 bg-light rounded">
                                                    {{ $cliente->notas_medicas }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($cliente->pasajeros->isNotEmpty())
                                    <div class="mt-4">
                                        <h5 class="mb-3 text-primary">
                                            <i class="ri-group-line"></i> Pasajeros Vinculados
                                        </h5>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Pasajero</th>
                                                    <th>Reserva</th>
                                                    <th>Tour</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($cliente->pasajeros as $pasajero)
                                                    <tr>
                                                        <td><a href="{{ route('admin.pasajeros.show', $pasajero) }}">{{ $pasajero->nombre_completo }}</a></td>
                                                        <td><a href="{{ route('admin.reservas.show', $pasajero->reserva) }}">{{ $pasajero->reserva?->codigo_reserva ?? '—' }}</a></td>
                                                        <td><a href="{{ route('admin.tours.show', $pasajero->tour) }}">{{ $pasajero->tour?->nombre_tour ?? '—' }}</a></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <!-- Fechas del sistema -->
                                <div class="mt-5 border-top pt-3">
                                    <small class="text-muted">
                                        Creado el: {{ $cliente->created_at->format('d/m/Y H:i') }} &nbsp; | &nbsp;
                                        Última actualización: {{ $cliente->updated_at->format('d/m/Y H:i') }}
                                    </small>
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

</body>
</html>
