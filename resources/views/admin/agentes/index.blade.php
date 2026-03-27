@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Agentes']); ?>
    @include('layouts.head-css')
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Lista de Agentes</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.agentes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-middle"></i> Nuevo Agente
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
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show">
                                        <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                <!-- Filtros -->
                                <form method="GET" class="mb-3 d-flex gap-2 flex-wrap" data-auto-filter="true">
                                    <div class="input-group" style="max-width:350px">
                                        <input type="text" name="buscar" class="form-control form-control-sm"
                                               placeholder="Buscar por nombre, email o código..."
                                               value="{{ request('buscar') }}">
                                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                                            <i class="ri-search-line"></i>
                                        </button>
                                    </div>
                                    <select name="estado" class="form-select form-select-sm" style="max-width:160px" onchange="this.form.submit()">
                                        <option value="">Todos los estados</option>
                                        <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                        <option value="vacaciones" {{ request('estado') == 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                                        <option value="baja" {{ request('estado') == 'baja' ? 'selected' : '' }}>Baja</option>
                                    </select>
                                    @if(request()->hasAny(['buscar','estado']))
                                        <a href="{{ route('admin.agentes.index') }}" class="btn btn-outline-danger btn-sm">
                                            <i class="ri-close-line"></i> Limpiar
                                        </a>
                                    @endif
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Código</th>
                                                <th>Nombre Completo</th>
                                                <th>Email</th>
                                                <th>Celular</th>
                                                <th>Departamento</th>
                                                <th>Comisión</th>
                                                <th>Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($agentes as $agente)
                                                <tr>
                                                    <td><span class="badge bg-secondary">{{ $agente->codigo_agente ?? '—' }}</span></td>
                                                    <td>
                                                        <a href="{{ route('admin.agentes.show', $agente) }}" class="text-body fw-medium">
                                                            {{ $agente->nombre_completo }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $agente->email }}</td>
                                                    <td>{{ $agente->celular }}</td>
                                                    <td>{{ $agente->departamento ?? '—' }}</td>
                                                    <td>{{ number_format($agente->comision_porcentaje, 1) }}%</td>
                                                    <td>
                                                        @php
                                                            $estadoClases = [
                                                                'activo'     => 'bg-success-subtle text-success border border-success-subtle',
                                                                'inactivo'   => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                                                                'vacaciones' => 'bg-info-subtle text-info border border-info-subtle',
                                                                'baja'       => 'bg-danger-subtle text-danger border border-danger-subtle',
                                                            ];
                                                        @endphp
                                                        <span class="badge {{ $estadoClases[$agente->estado] ?? '' }}">
                                                            {{ ucfirst($agente->estado) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.agentes.show', $agente) }}"
                                                           class="btn btn-info btn-sm">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <a href="{{ route('admin.agentes.edit', $agente) }}"
                                                           class="btn btn-warning btn-sm">
                                                            <i class="ri-pencil-line"></i>
                                                        </a>
                                                        <form action="{{ route('admin.agentes.destroy', $agente) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('¿Eliminar este agente?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-danger btn-sm">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center text-muted py-4">
                                                        No hay agentes registrados.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{ $agentes->withQueryString()->links() }}

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
