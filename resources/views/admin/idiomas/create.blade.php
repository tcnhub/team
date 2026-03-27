@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Crear Idioma']); ?>
    @include('layouts.head-css')
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Nuevo Idioma</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.idiomas.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="ri-arrow-left-line align-middle"></i> Volver
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @include('admin.idiomas.partials.form')
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
