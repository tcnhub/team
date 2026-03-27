@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Lista de Clientes']); ?>

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
                                <h4 class="card-title mb-0 flex-grow-1">Lista de Clientes</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.clientes.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-middle"></i> Nuevo Cliente
                                    </a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <!-- ==================== FILTROS DE BÚSQUEDA ==================== -->
                                <form method="GET" action="{{ route('admin.clientes.index') }}" class="mb-4" data-auto-filter="true">
                                    <div class="row g-3">

                                        <!-- Búsqueda general -->
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Buscar Cliente</label>
                                            <input type="text" name="buscar" class="form-control"
                                                   value="{{ request('buscar') }}"
                                                   placeholder="Nombre, Apellido, DNI o Email">
                                        </div>

                                        <!-- País -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">País</label>
                                            <select name="pais_id" class="form-select">
                                                <option value="">Todos los países</option>
                                                @foreach($paises ?? [] as $pais)
                                                    <option value="{{ $pais->id }}"
                                                        {{ request('pais_id') == $pais->id ? 'selected' : '' }}>
                                                        {{ $pais->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Estado -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Estado</label>
                                            <select name="estado" class="form-select">
                                                <option value="">Todos</option>
                                                <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                                                <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                                            </select>
                                        </div>

                                        <!-- Tipo de Documento -->
                                        <div class="col-md-2">
                                            <label class="form-label fw-semibold">Tipo Doc.</label>
                                            <select name="tipo_documento" class="form-select">
                                                <option value="">Todos</option>
                                                <option value="passport" {{ request('tipo_documento') === 'passport' ? 'selected' : '' }}>Passport</option>
                                                <option value="dni" {{ request('tipo_documento') === 'dni' ? 'selected' : '' }}>DNI</option>
                                                <option value="id" {{ request('tipo_documento') === 'id' ? 'selected' : '' }}>ID</option>
                                            </select>
                                        </div>

                                    </div>

                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ri-search-line"></i> Buscar
                                            </button>
                                            <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary ms-2">
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
                                                <th scope="col">Cliente</th>
                                                <th scope="col">Documento</th>
                                                <th scope="col">País</th>
                                                <th scope="col">Contacto</th>
                                                <th scope="col">Fecha Nacimiento</th>
                                                <th scope="col">Estado</th>
                                                <th scope="col" width="140">Acciones</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @forelse ($clientes as $cliente)
                                                <tr>
                                                    <td>{{ $cliente->id }}</td>
                                                    <td>
                                                        <strong>{{ $cliente->nombre_completo ?? $cliente->nombre . ' ' . $cliente->apellido }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            @if($cliente->genero)
                                                                {{ $cliente->genero == 'male' ? '♂' : ($cliente->genero == 'female' ? '♀' : '') }}
                                                            @endif
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">
                                                            {{ strtoupper($cliente->tipo_documento) }}
                                                        </span>
                                                        <br>
                                                        <strong>{{ $cliente->numero_documento }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($cliente->pais)
                                                            {{ $cliente->pais->nombre }}
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($cliente->email)
                                                            <a href="mailto:{{ $cliente->email }}" class="text-primary">
                                                                {{ $cliente->email }}
                                                            </a>
                                                            <br>
                                                        @endif
                                                        @if($cliente->whatsapp)
                                                            <small class="text-success">WhatsApp: {{ $cliente->whatsapp }}</small>
                                                        @elseif($cliente->telefono)
                                                            <small>Tel: {{ $cliente->telefono }}</small>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($cliente->fecha_nacimiento)
                                                            {{ $cliente->fecha_nacimiento->format('d/m/Y') }}
                                                            <small class="text-muted d-block">
                                                                ({{ $cliente->edad ?? '—' }} años)
                                                            </small>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $estadoClass = $cliente->activo
                                                                ? 'bg-success'
                                                                : 'bg-danger';
                                                        @endphp
                                                        <span class="badge {{ $estadoClass }} px-3 py-2">
                                                            {{ $cliente->activo ? 'Activo' : 'Inactivo' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="hstack gap-2">
                                                            <a href="{{ route('admin.clientes.show', $cliente) }}"
                                                               class="btn btn-sm btn-soft-info" title="Ver detalle">
                                                                <i class="ri-eye-line"></i>
                                                            </a>
                                                            <a href="{{ route('admin.clientes.edit', $cliente) }}"
                                                               class="btn btn-sm btn-soft-warning" title="Editar">
                                                                <i class="ri-pencil-line"></i>
                                                            </a>
                                                            <form action="{{ route('admin.clientes.destroy', $cliente) }}"
                                                                  method="POST" class="d-inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="button" class="btn btn-sm btn-soft-danger delete-btn"
                                                                        title="Eliminar"
                                                                        data-name="{{ $cliente->nombre_completo ?? $cliente->nombre . ' ' . $cliente->apellido }}">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-5">
                                                        <i class="ri-user-line display-4 text-muted"></i>
                                                        <p class="mt-3 mb-0">No hay clientes registrados</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginación -->
                                    @if($clientes->hasPages())
                                        <div class="d-flex justify-content-between align-items-center mt-4 px-2">
                                            <div class="text-muted small">
                                                Mostrando <strong>{{ $clientes->firstItem() ?? 0 }}</strong> al
                                                <strong>{{ $clientes->lastItem() ?? 0 }}</strong> de
                                                <strong>{{ $clientes->total() }}</strong> clientes
                                            </div>
                                            <nav aria-label="Page navigation">
                                                {{ $clientes->links('pagination::bootstrap-5') }}
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
            if (confirm(`¿Estás seguro de eliminar al cliente "${name}"?\n\nEsta acción no se puede deshacer.`)) {
                this.closest('form').submit();
            }
        });
    });
</script>

</body>
</html>
