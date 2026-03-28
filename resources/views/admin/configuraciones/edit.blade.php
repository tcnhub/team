@include('layouts.main')
<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Editar Configuración']); ?>
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
                        <h4 class="card-title mb-0">Editar Configuración</h4>
                        <a href="{{ route('admin.configuraciones.index') }}" class="btn btn-secondary btn-sm">Volver</a>
                    </div>
                    <div class="card-body">
                        @include('admin.configuraciones.partials.form', ['configuracion' => $configuracion])
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
