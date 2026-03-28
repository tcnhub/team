@include('layouts.main')
<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Detalle Addon']); ?>
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
                        <h4 class="card-title mb-0">{{ $addon->nombre }}</h4>
                        <a href="{{ route('admin.addons.index') }}" class="btn btn-secondary btn-sm">Volver</a>
                    </div>
                    <div class="card-body">
                        <p><strong>Monto:</strong> USD {{ number_format($addon->monto, 2) }}</p>
                        <p><strong>Descripción:</strong> {{ $addon->descripcion ?: '—' }}</p>
                        <p><strong>Tours vinculados:</strong> {{ $addon->tours->count() }}</p>
                        <p><strong>Reservas con addon:</strong> {{ $addon->reservas->count() }}</p>
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
