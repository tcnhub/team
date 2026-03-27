@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Agregar Año al Calendario']); ?>
    @include('layouts.head-css')
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                <div class="d-flex align-items-center gap-2 mb-4">
                    <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-secondary btn-sm">
                        <i class="ri-arrow-left-line"></i>
                    </a>
                    <h4 class="mb-0">Agregar Año de Disponibilidad — {{ $tour->nombre_tour }}</h4>
                </div>

                <div class="row">
                    <div class="col-md-6 col-lg-5">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0"><i class="ri-calendar-add-line me-1"></i>Generar Calendario</h5>
                            </div>
                            <div class="card-body">

                                @if($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form action="{{ route('admin.tours.calendar.store', $tour) }}" method="POST">
                                    @csrf

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Año <span class="text-danger">*</span></label>
                                        <input type="number" name="anio" min="2020" max="2100"
                                               class="form-control @error('anio') is-invalid @enderror"
                                               value="{{ old('anio', now()->year) }}" required>
                                        @error('anio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($aniosExistentes->isNotEmpty())
                                            <div class="form-text">
                                                Años ya generados:
                                                @foreach($aniosExistentes as $a)
                                                    <span class="badge bg-secondary">{{ $a }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Capacidad diaria para este año</label>
                                        <input type="number" name="capacidad_anio"
                                               min="1"
                                               max="{{ $tour->capacidad_maxima }}"
                                               class="form-control @error('capacidad_anio') is-invalid @enderror"
                                               value="{{ old('capacidad_anio') }}"
                                               placeholder="{{ $tour->capacidad_diaria }}">
                                        @error('capacidad_anio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            Déjalo vacío para usar el default del tour
                                            ({{ number_format($tour->capacidad_diaria) }} esp/día).
                                            Máximo: {{ number_format($tour->capacidad_maxima) }}.
                                        </div>
                                    </div>

                                    <div class="alert alert-info small">
                                        <i class="ri-information-line me-1"></i>
                                        Se generarán automáticamente todos los días del año,
                                        incluyendo el 29 de febrero si es bisiesto.
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ri-calendar-add-line me-1"></i>Generar Calendario
                                        </button>
                                        <a href="{{ route('admin.tours.show', $tour) }}"
                                           class="btn btn-outline-secondary">Cancelar</a>
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
