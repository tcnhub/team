@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Lista de Pasajeros']); ?>
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
                                <h4 class="card-title mb-0 flex-grow-1">Lista de Pasajeros</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.pasajeros.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-middle"></i> Nuevo Pasajero
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.pasajeros.index') }}" class="mb-4" data-auto-filter="true">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Buscar</label>
                                            <input type="text" name="buscar" class="form-control" value="{{ request('buscar') }}"
                                                   placeholder="Nombre, documento o email">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Estado</label>
                                            <select name="estado" class="form-select">
                                                <option value="">Todos</option>
                                                <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                                                <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Tipo Doc.</label>
                                            <select name="tipo_documento" class="form-select">
                                                <option value="">Todos</option>
                                                <option value="passport" {{ request('tipo_documento') === 'passport' ? 'selected' : '' }}>Passport</option>
                                                <option value="dni" {{ request('tipo_documento') === 'dni' ? 'selected' : '' }}>DNI</option>
                                                <option value="id" {{ request('tipo_documento') === 'id' ? 'selected' : '' }}>ID</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">País</label>
                                            <select name="pais_id" class="form-select">
                                                <option value="">Todos</option>
                                                @foreach($paises as $pais)
                                                    <option value="{{ $pais->id }}" {{ request('pais_id') == $pais->id ? 'selected' : '' }}>{{ $pais->nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Cliente</label>
                                            <select name="cliente_id" class="form-select">
                                                <option value="">Todos</option>
                                                @foreach($clientes as $cliente)
                                                    <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre_completo }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Reserva</label>
                                            <select name="reserva_id" class="form-select">
                                                <option value="">Todas</option>
                                                @foreach($reservas as $reserva)
                                                    <option value="{{ $reserva->id }}" {{ request('reserva_id') == $reserva->id ? 'selected' : '' }}>{{ $reserva->codigo_reserva }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-semibold">Tour</label>
                                            <select name="tour_id" class="form-select">
                                                <option value="">Todos</option>
                                                @foreach($tours as $tour)
                                                    <option value="{{ $tour->id }}" {{ request('tour_id') == $tour->id ? 'selected' : '' }}>{{ $tour->nombre_tour }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-search-line"></i> Buscar
                                        </button>
                                        <a href="{{ route('admin.pasajeros.index') }}" class="btn btn-secondary ms-2">
                                            <i class="ri-refresh-line"></i> Limpiar
                                        </a>
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table align-middle mb-0 table-hover">
                                        <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Pasajero</th>
                                            <th>Documento</th>
                                            <th>Contacto</th>
                                            <th>Cliente</th>
                                            <th>Reserva</th>
                                            <th>Tour</th>
                                            <th>Estado</th>
                                            <th width="140">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($pasajeros as $pasajero)
                                            <tr>
                                                <td>{{ $pasajero->id }}</td>
                                                <td>
                                                    <strong>{{ $pasajero->nombre_completo }}</strong>
                                                    <div class="text-muted small">
                                                        {{ $pasajero->pais?->nombre ?? 'Sin país' }}
                                                        @if($pasajero->fecha_nacimiento)
                                                            · {{ $pasajero->fecha_nacimiento->format('d/m/Y') }}
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ strtoupper($pasajero->tipo_documento) }}</span>
                                                    <div class="fw-semibold">{{ $pasajero->numero_documento }}</div>
                                                </td>
                                                <td>
                                                    <div>{{ $pasajero->email ?? '—' }}</div>
                                                    <small class="text-muted">{{ $pasajero->whatsapp ?: ($pasajero->telefono ?: '—') }}</small>
                                                </td>
                                                <td>
                                                    @if($pasajero->cliente)
                                                        <a href="{{ route('admin.clientes.show', $pasajero->cliente) }}">{{ $pasajero->cliente->nombre_completo }}</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($pasajero->reserva)
                                                        <a href="{{ route('admin.reservas.show', $pasajero->reserva) }}">{{ $pasajero->reserva->codigo_reserva }}</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($pasajero->tour)
                                                        <a href="{{ route('admin.tours.show', $pasajero->tour) }}">{{ $pasajero->tour->nombre_tour }}</a>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge {{ $pasajero->activo ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $pasajero->activo ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="hstack gap-2">
                                                        <a href="{{ route('admin.pasajeros.show', $pasajero) }}" class="btn btn-sm btn-soft-info">
                                                            <i class="ri-eye-line"></i>
                                                        </a>
                                                        <a href="{{ route('admin.pasajeros.edit', $pasajero) }}" class="btn btn-sm btn-soft-warning">
                                                            <i class="ri-pencil-line"></i>
                                                        </a>
                                                        <form action="{{ route('admin.pasajeros.destroy', $pasajero) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-soft-danger" onclick="return confirm('¿Eliminar este pasajero?')">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-5">
                                                    <i class="ri-user-3-line display-4 text-muted"></i>
                                                    <p class="mt-3 mb-0">No hay pasajeros registrados</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                @if($pasajeros->hasPages())
                                    <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                                        <div class="text-muted small">
                                            Mostrando <strong>{{ $pasajeros->firstItem() ?? 0 }}</strong> al
                                            <strong>{{ $pasajeros->lastItem() ?? 0 }}</strong> de
                                            <strong>{{ $pasajeros->total() }}</strong> pasajeros
                                        </div>
                                        {{ $pasajeros->links('pagination::bootstrap-5') }}
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
</body>
</html>
