@include('layouts.main')

<head>

    <?php includeFileWithVariables('layouts/title-meta.php', array('title' => 'Editar Tour')); ?>

        <!-- jsvectormap css -->
    <link href="{{ asset('assets/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Swiper slider css -->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    @include('layouts.head-css')

</head>

<body>

<!-- Begin page -->
<div id="layout-wrapper">

    @include('layouts.menu')

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Editar Tour</h4>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary btn-sm">
                                        <i class="ri-arrow-left-line align-middle"></i> Volver a la lista
                                    </a>
                                </div>
                            </div><!-- end card header -->

                            <div class="card-body">

                                <!-- ==================== FORMULARIO (PARTIAL REUTILIZABLE) ==================== -->
                                @include('admin.tours.partials.form', ['tour' => $tour])
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

<!-- apexcharts -->
<script src="{{ asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>

<!-- Vector map-->
<script src="{{ asset('assets/libs/jsvectormap/jsvectormap.min.js') }}"></script>
<script src="{{ asset('assets/libs/jsvectormap/maps/world-merc.js') }}"></script>

<!-- Swiper slider js -->
<script src="{{ asset('assets/libs/swiper/swiper-bundle.min.js') }}"></script>

<!-- Dashboard init -->
<script src="{{ asset('assets/js/pages/dashboard-ecommerce.init.js') }}"></script>

<!-- App js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

</body>

</html>
