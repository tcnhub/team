@include('layouts.main')
<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Registrar Pago']); ?>
    @include('layouts.head-css')
</head>
<body>
<div id="layout-wrapper">
    @include('layouts.menu')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-9 mx-auto">
                        <div class="card">
                            <div class="card-header d-flex align-items-center">
                                <h4 class="card-title mb-0 flex-grow-1">Registrar Nuevo Pago</h4>
                                <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ri-arrow-left-line"></i> Volver
                                </a>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.pagos.store') }}" method="POST">
                                    @csrf
                                    @include('admin.pagos.partials.form')
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-save-line me-1"></i>Guardar Pago
                                        </button>
                                        <a href="{{ route('admin.pagos.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
                                    </div>
                                </form>
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
