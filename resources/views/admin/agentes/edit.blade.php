@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Editar Agente']); ?>
    @include('layouts.head-css')
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-10">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Editar Agente: {{ $agente->nombre_completo }}</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.agentes.show', $agente) }}" class="btn btn-secondary btn-sm me-2">
                                        <i class="ri-eye-line align-middle"></i> Ver
                                    </a>
                                    <a href="{{ route('admin.agentes.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="ri-arrow-left-line align-middle"></i> Lista
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @include('admin.agentes.partials.form')
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
