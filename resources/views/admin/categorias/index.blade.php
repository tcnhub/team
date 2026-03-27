@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Categorías de Tours']); ?>
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
                                <h4 class="card-title mb-0 flex-grow-1">Categorías de Tours</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-middle"></i> Nueva Categoría
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

                                <form method="GET" class="mb-3">
                                    <div class="input-group" style="max-width:380px">
                                        <input type="text" name="buscar" class="form-control form-control-sm"
                                               placeholder="Buscar categoría..."
                                               value="{{ request('buscar') }}">
                                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                                            <i class="ri-search-line"></i>
                                        </button>
                                        @if(request('buscar'))
                                            <a href="{{ route('admin.categorias.index') }}" class="btn btn-outline-danger btn-sm">
                                                <i class="ri-close-line"></i>
                                            </a>
                                        @endif
                                    </div>
                                </form>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th>Color</th>
                                                <th>Ícono</th>
                                                <th>Estado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($categorias as $categoria)
                                                <tr>
                                                    <td>{{ $categoria->id }}</td>
                                                    <td>
                                                        @if($categoria->icono)
                                                            <i class="{{ $categoria->icono }} me-1"
                                                               style="color:{{ $categoria->color ?? '#333' }}"></i>
                                                        @endif
                                                        <strong>{{ $categoria->nombre }}</strong>
                                                    </td>
                                                    <td>
                                                        @if($categoria->color)
                                                            <span class="badge" style="background:{{ $categoria->color }}">
                                                                {{ $categoria->color }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $categoria->icono ?? '—' }}</td>
                                                    <td>
                                                        @if($categoria->activo)
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Activa</span>
                                                        @else
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactiva</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.categorias.edit', $categoria) }}"
                                                           class="btn btn-warning btn-sm">
                                                            <i class="ri-pencil-line"></i>
                                                        </a>
                                                        <form action="{{ route('admin.categorias.destroy', $categoria) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('¿Eliminar esta categoría?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-danger btn-sm">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted py-4">
                                                        No hay categorías registradas.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{ $categorias->withQueryString()->links() }}

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
