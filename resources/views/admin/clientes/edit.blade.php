@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Editar Cliente']); ?>

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
                                <h4 class="card-title mb-0 flex-grow-1">Editar Cliente</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="ri-arrow-left-line align-middle"></i> Volver a la lista
                                    </a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <!-- ==================== FORMULARIO (PARTIAL REUTILIZABLE) ==================== -->
                                @include('admin.clientes.partials.form', ['cliente' => $cliente])
                                <!-- ======================================================== -->

                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div>

            </div>
            <!-- container-fluid -->
        </div>
        <!-- End Page-content -->

        @include('layouts.footer')
    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->

@include('layouts.customizer')
@include('layouts.vendor-scripts')

<script src="{{ asset('assets/js/app.js') }}"></script>

</body>
</html>
