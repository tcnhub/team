@include('layouts.main')
<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Configuraciones']); ?>
    @include('layouts.head-css')
</head>
<body>
<div id="layout-wrapper">
    @include('layouts.menu')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Configuraciones</h4>
                        <a href="{{ route('admin.configuraciones.create') }}" class="btn btn-primary btn-sm"><i class="ri-add-line"></i> Nueva Configuración</a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                        <form method="GET" class="mb-3 d-flex gap-2">
                            <input type="text" name="buscar" class="form-control form-control-sm" style="max-width:360px" placeholder="Buscar..." value="{{ request('buscar') }}">
                            <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="ri-search-line"></i></button>
                        </form>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light"><tr><th>#</th><th>Nombre</th><th>Valor</th><th class="text-center">Acciones</th></tr></thead>
                                <tbody>
                                @forelse($configuraciones as $configuracion)
                                    <tr>
                                        <td>{{ $configuracion->id }}</td>
                                        <td><a href="{{ route('admin.configuraciones.show', $configuracion) }}">{{ $configuracion->nombre }}</a></td>
                                        <td>{{ \Illuminate\Support\Str::limit($configuracion->valor, 90) ?: '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.configuraciones.edit', $configuracion) }}" class="btn btn-warning btn-sm"><i class="ri-pencil-line"></i></a>
                                            <form action="{{ route('admin.configuraciones.destroy', $configuracion) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta configuración?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm"><i class="ri-delete-bin-line"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No hay configuraciones registradas.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $configuraciones->links() }}
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
