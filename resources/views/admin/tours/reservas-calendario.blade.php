@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => 'Reservas — ' . $tour->nombre_tour]); ?>
    @include('layouts.head-css')
    <style>
        .res-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 3px;
        }
        .res-header-day {
            text-align: center;
            font-size: .70rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            padding: 3px 0 5px;
        }
        .res-cell {
            min-height: 80px;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 4px 5px;
            background: #fff;
            font-size: .72rem;
            vertical-align: top;
        }
        .res-cell.empty        { background: transparent; border-color: transparent; }
        .res-cell.today        { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,.18); }
        .res-day-num {
            font-size: .75rem;
            font-weight: 700;
            color: #495057;
            line-height: 1;
            margin-bottom: 3px;
        }
        .res-cell.today .res-day-num {
            background: #0d6efd;
            color: #fff;
            width: 20px; height: 20px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .res-badge {
            display: block;
            border-radius: 4px;
            padding: 1px 4px;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: .68rem;
            cursor: pointer;
            line-height: 1.4;
        }
        .res-badge.inicio  { background: #d1e7dd; color: #0f5132; border-left: 3px solid #198754; }
        .res-badge.medio   { background: #cfe2ff; color: #084298; border-left: 3px solid #0d6efd; }
        .res-badge.fin     { background: #f8d7da; color: #842029; border-left: 3px solid #dc3545; }
        .res-badge.unico   { background: #fff3cd; color: #664d03; border-left: 3px solid #ffc107; }

        .month-card { margin-bottom: 2rem; }
        .month-title {
            font-size: 1rem;
            font-weight: 700;
            padding: .5rem 1rem;
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }
    </style>
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                {{-- Breadcrumb --}}
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">Reservas: {{ $tour->nombre_tour }}</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="{{ route('admin.tours.index') }}">Tours</a></li>
                                    <li class="breadcrumb-item active">Reservas Calendario</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                {{-- Header con filtro de año --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body py-2 d-flex flex-wrap align-items-center gap-3">
                                <div>
                                    <span class="fw-semibold">Tour:</span> {{ $tour->nombre_tour }}
                                    @if($tour->duracion_dias)
                                        <span class="ms-2 badge bg-info">{{ $tour->duracion_dias }} días</span>
                                    @endif
                                    @if($tour->codigo_tour)
                                        <span class="ms-1 badge bg-secondary">{{ $tour->codigo_tour }}</span>
                                    @endif
                                </div>
                                <form method="GET" class="d-flex align-items-center gap-2 ms-auto">
                                    <label class="fw-semibold mb-0">Año:</label>
                                    <select name="anio" class="form-select form-select-sm" style="width:100px" onchange="this.form.submit()">
                                        @foreach($aniosDisponibles as $a)
                                            <option value="{{ $a }}" {{ $a == $anio ? 'selected' : '' }}>{{ $a }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                <a href="{{ route('admin.tours.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="ri-arrow-left-line"></i> Volver a Tours
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Leyenda --}}
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="res-badge inicio d-inline-block" style="width:auto;cursor:default">Día inicio</span>
                            <span class="res-badge medio d-inline-block" style="width:auto;cursor:default">Días intermedios</span>
                            <span class="res-badge fin d-inline-block" style="width:auto;cursor:default">Día fin</span>
                            <span class="res-badge unico d-inline-block" style="width:auto;cursor:default">Tour de 1 día</span>
                            <span class="text-muted small ms-auto">
                                <strong>{{ $reservas->count() }}</strong> reservas en {{ $anio }}
                            </span>
                        </div>
                    </div>
                </div>

                @php
                    $meses = [
                        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
                    ];
                    $hoy = \Carbon\Carbon::today();
                @endphp

                @foreach($meses as $numMes => $nombreMes)
                    @php
                        $primerDia   = \Carbon\Carbon::create($anio, $numMes, 1);
                        $ultimoDia   = $primerDia->copy()->endOfMonth();
                        $offsetInicio = ($primerDia->dayOfWeek + 6) % 7; // Lunes=0
                    @endphp

                    <div class="card month-card">
                        <div class="month-title">{{ $nombreMes }} {{ $anio }}</div>
                        <div class="card-body p-2">
                            <div class="res-grid">
                                {{-- Encabezados días --}}
                                @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d)
                                    <div class="res-header-day">{{ $d }}</div>
                                @endforeach

                                {{-- Celdas vacías al inicio --}}
                                @for($i = 0; $i < $offsetInicio; $i++)
                                    <div class="res-cell empty"></div>
                                @endfor

                                {{-- Días del mes --}}
                                @for($dia = 1; $dia <= $ultimoDia->day; $dia++)
                                    @php
                                        $fechaActual = \Carbon\Carbon::create($anio, $numMes, $dia);
                                        $fechaKey    = $fechaActual->format('Y-m-d');
                                        $esHoy       = $fechaActual->isSameDay($hoy);
                                        $reservasDia = $mapaFechas[$fechaKey] ?? [];
                                    @endphp
                                    <div class="res-cell {{ $esHoy ? 'today' : '' }}">
                                        <div class="res-day-num">{{ $dia }}</div>
                                        @foreach($reservasDia as $res)
                                            @php
                                                $esInicio = $res->fecha_inicio->isSameDay($fechaActual);
                                                $esFin    = $res->fecha_fin_calculada->isSameDay($fechaActual);
                                                $esUnico  = $esInicio && $esFin;
                                                if ($esUnico)        $tipo = 'unico';
                                                elseif ($esInicio)   $tipo = 'inicio';
                                                elseif ($esFin)      $tipo = 'fin';
                                                else                 $tipo = 'medio';

                                                $nombre = $res->cliente?->nombre_completo ?? $res->codigo_reserva;
                                            @endphp
                                            <span class="res-badge {{ $tipo }}"
                                                  title="{{ $res->codigo_reserva }} — {{ $nombre }}&#10;{{ $res->fecha_inicio->format('d/m') }} → {{ $res->fecha_fin_calculada->format('d/m') }}&#10;Estado: {{ $res->estado_texto }}"
                                                  data-bs-toggle="tooltip" data-bs-placement="top"
                                                  onclick="window.location='{{ route('admin.reservas.show', $res) }}'">
                                                @if($esInicio || $esUnico)
                                                    <i class="ri-user-line"></i>
                                                @endif
                                                {{ \Illuminate\Support\Str::limit($nombre, 14) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endfor

                                {{-- Celdas vacías al final para completar la semana --}}
                                @php
                                    $totalCeldas = $offsetInicio + $ultimoDia->day;
                                    $resto = $totalCeldas % 7;
                                    $vacias = $resto > 0 ? 7 - $resto : 0;
                                @endphp
                                @for($i = 0; $i < $vacias; $i++)
                                    <div class="res-cell empty"></div>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>{{-- container-fluid --}}
        </div>{{-- page-content --}}

        @include('layouts.footer')
    </div>
</div>

@include('layouts.customizer')
@include('layouts.vendor-scripts')
<script src="{{ asset('assets/js/app.js') }}"></script>
<script>
    // Activar tooltips de BS5
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, { html: true });
    });
</script>
</body>
</html>
