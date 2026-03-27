@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Países']); ?>
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
                                <h4 class="card-title mb-0 flex-grow-1">Lista de Países</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.paises.create') }}" class="btn btn-primary btn-sm">
                                        <i class="ri-add-line align-middle"></i> Nuevo País
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

                                <form method="GET" class="mb-3" data-auto-filter="true">
                                    <div class="input-group" style="max-width:380px">
                                        <input type="text" name="buscar" class="form-control form-control-sm"
                                               placeholder="Buscar por nombre o código ISO..."
                                               value="{{ request('buscar') }}">
                                        <button class="btn btn-outline-secondary btn-sm" type="submit">
                                            <i class="ri-search-line"></i>
                                        </button>
                                        @if(request('buscar'))
                                            <a href="{{ route('admin.paises.index') }}" class="btn btn-outline-danger btn-sm">
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
                                                <th>Código ISO</th>
                                                <th>Creado</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paises as $pais)
                                                <tr>
                                                    <td>{{ $pais->id }}</td>
                                                    <td><strong>{{ $pais->nombre }}</strong></td>
                                                    <td>
                                                        @if($pais->codigo_iso)
                                                            <span class="badge bg-secondary">{{ $pais->codigo_iso }}</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $pais->created_at->format('d/m/Y') }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('admin.paises.edit', $pais) }}"
                                                           class="btn btn-warning btn-sm">
                                                            <i class="ri-pencil-line"></i>
                                                        </a>
                                                        <form action="{{ route('admin.paises.destroy', $pais) }}"
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('¿Eliminar este país?')">
                                                            @csrf @method('DELETE')
                                                            <button class="btn btn-danger btn-sm">
                                                                <i class="ri-delete-bin-line"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        No hay países registrados.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                {{ $paises->withQueryString()->links() }}

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
