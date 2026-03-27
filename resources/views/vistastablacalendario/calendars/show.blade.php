@extends('layouts.app')

@section('title', $tour->nombre . ' — ' . $calendar->anio)

@section('head')
    <style>
        .cal-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
        }
        .cal-header-day {
            text-align: center;
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            padding: 4px 0 6px;
        }
        .cal-cell {
            min-height: 100px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 6px 7px 5px;
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 3px;
            transition: box-shadow .15s;
        }
        .cal-cell:hover        { box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .cal-cell.empty        { background: transparent; border-color: transparent; pointer-events: none; }
        .cal-cell.lleno        { background: #fff5f5; border-color: #fcc; }
        .cal-cell.casi-lleno   { background: #fffbf0; border-color: #ffe08a; }
        .cal-cell.today        { border-color: #0d6efd; box-shadow: 0 0 0 2px rgba(13,110,253,.2); }
        .cal-cell.bloqueado    { background: #f8f9fa; border-color: #dee2e6; }

        .cal-day-num {
            font-size: .8rem;
            font-weight: 600;
            color: #343a40;
            line-height: 1;
        }
        .cal-cell.today .cal-day-num {
            background: #0d6efd;
            color: #fff;
            width: 22px; height: 22px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cal-progress {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            overflow: hidden;
        }
        .cal-progress-bar {
            height: 100%;
            border-radius: 2px;
            transition: width .4s ease;
        }

        /* Pulso de la barra al guardar */
        @keyframes barSave {
            0%   { opacity: 1;   transform: scaleY(1); }
            30%  { opacity: .3;  transform: scaleY(2.5); }
            60%  { opacity: .9;  transform: scaleY(1.2); }
            100% { opacity: 1;   transform: scaleY(1); }
        }
        .cal-progress-bar.bar-saved {
            animation: barSave .45s ease-out forwards;
            transform-origin: bottom;
        }

        .cal-stat        { font-size: .67rem; color: #6c757d; line-height: 1.3; }
        .cal-stat strong { font-weight: 600; }

        /* Inline edit */
        .spaces-display           { cursor: pointer; user-select: none; }
        .spaces-display:hover     { text-decoration: underline dotted; }
        .spaces-input-wrap        { display: none; }
        .spaces-input-wrap.active { display: flex; gap: 3px; align-items: center; }
        .spaces-display.hidden    { display: none; }

        /* Feedback verde del input al guardar */
        .spaces-input-wrap .spaces-num-input {
            transition: border-color .25s, box-shadow .25s;
        }
        .spaces-input-wrap.saved .spaces-num-input {
            border-color: #198754 !important;
            box-shadow: 0 0 0 2px rgba(25,135,84,.3) !important;
        }

        /* Botones de celda */
        .cal-actions { display: flex; gap: 3px; margin-top: auto; }
        .cal-btn {
            flex: 1;
            font-size: .63rem;
            padding: 2px 0;
            border-radius: 4px;
            border: 1px solid;
            background: transparent;
            cursor: pointer;
            text-align: center;
            white-space: nowrap;
            line-height: 1.5;
        }
        .cal-btn-reserve          { color: #198754; border-color: #198754; }
        .cal-btn-reserve:hover    { background: #198754; color: #fff; }
        .cal-btn-reserve:disabled { opacity: .4; cursor: not-allowed; }

        .mes-title { font-size: 1rem; font-weight: 600; color: #212529; margin-bottom: 10px; }

        .badge-bloqueado {
            font-size: .6rem;
            padding: 1px 5px;
            border-radius: 20px;
            background: #e9ecef;
            color: #6c757d;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tours.show', $tour) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0 fw-semibold">
                {{ $tour->nombre }}
                <span class="text-muted fw-normal">/ {{ $calendar->anio }}</span>
                @if($calendar->es_bisiesto)
                    <span class="badge bg-info-subtle text-info border border-info-subtle ms-1" style="font-size:.7rem">Bisiesto</span>
                @endif
            </h4>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <select name="mes" class="form-select form-select-sm" onchange="this.form.submit()" style="width:155px">
                    <option value="0" {{ $mesFiltro == 0 ? 'selected' : '' }}>Todo el año</option>
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $mesFiltro == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create($calendar->anio, $m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </form>
            <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalBulk">
                <i class="bi bi-pencil-square me-1"></i>Editar en bloque
            </button>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="d-flex gap-3 mb-3 flex-wrap align-items-center" style="font-size:.75rem;color:#555">
        <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#f0fdf4;border:1px solid #86efac"></span>Disponible</span>
        <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#fffbf0;border:1px solid #ffe08a"></span>Casi lleno (&gt;75%)</span>
        <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#fff5f5;border:1px solid #fcc"></span>Sin disponibilidad</span>
        <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#f8f9fa;border:1px solid #dee2e6"></span>Bloqueado manualmente</span>
        <span class="ms-auto text-muted">
        <i class="bi bi-pencil me-1"></i>Clic en disponibles para editar · Tab pasa al día siguiente
    </span>
    </div>

    @php
        $nombresMeses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                         7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
        $diasSemana   = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
        $today        = \Carbon\Carbon::today()->toDateString();
        $capMax       = $tour->capacidad_maxima;
    @endphp

    @forelse($diasPorMes as $mes => $dias)
        @php $offsetInicio = \Carbon\Carbon::create($calendar->anio, $mes, 1)->dayOfWeekIso - 1; @endphp

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body pb-3">
                <div class="mes-title">{{ $nombresMeses[$mes] }} {{ $calendar->anio }}</div>
                <div class="cal-grid">

                    @foreach($diasSemana as $ds)
                        <div class="cal-header-day">{{ $ds }}</div>
                    @endforeach

                    @for($i = 0; $i < $offsetInicio; $i++)
                        <div class="cal-cell empty"></div>
                    @endfor

                    @foreach($dias as $day)
                        @php
                            $pct        = $day->capacidad_dia > 0 ? round(($day->espacios_usados / $day->capacidad_dia) * 100) : 100;
                            $lleno      = $day->espacios_disponibles <= 0;
                            $casilleno  = !$lleno && $pct >= 75;
                            $bloqManual = $day->espacios_bloqueados > 0;
                            $barColor   = $lleno ? '#dc3545' : ($casilleno ? '#ffc107' : '#198754');
                            $pctBarra   = $day->capacidad_dia > 0
                                            ? round((($day->espacios_usados + $day->espacios_bloqueados) / $day->capacidad_dia) * 100)
                                            : 100;
                            $isToday    = $day->fecha->toDateString() === $today;
                            $maxEditar  = $day->capacidad_dia - $day->espacios_usados;
                        @endphp

                        <div class="cal-cell
                                {{ $lleno ? 'lleno' : ($casilleno ? 'casi-lleno' : '') }}
                                {{ $bloqManual && !$lleno ? 'bloqueado' : '' }}
                                {{ $isToday ? 'today' : '' }}"
                             data-id="{{ $day->id }}"
                             data-fecha="{{ $day->fecha->toDateString() }}"
                             data-fecha-label="{{ $day->fecha->translatedFormat('d \d\e F') }}"
                             data-capacidad="{{ $day->capacidad_dia }}"
                             data-usados="{{ $day->espacios_usados }}"
                             data-bloqueados="{{ $day->espacios_bloqueados }}"
                             data-disponibles="{{ $day->espacios_disponibles }}"
                             data-max-editar="{{ $maxEditar }}">

                            <div class="cal-day-num">{{ $day->fecha->day }}</div>

                            <div class="cal-progress">
                                <div class="cal-progress-bar"
                                     style="width:{{ min($pctBarra,100) }}%;background:{{ $barColor }}"></div>
                            </div>

                            {{-- Disponibles: clicable para editar --}}
                            <div class="spaces-display cal-stat"
                                 title="Clic para cambiar disponibles"
                                 onclick="abrirEdicion(this)">
                                <strong style="color:{{ $barColor }}">{{ $day->espacios_disponibles }}</strong>
                                <span> disp.</span>
                                @if($bloqManual)
                                    <span class="badge-bloqueado d-block mt-1">
                                    <i class="bi bi-lock-fill" style="font-size:.55rem"></i>
                                    {{ $day->espacios_bloqueados }} bloq.
                                </span>
                                @endif
                            </div>

                            {{-- Input inline --}}
                            <div class="spaces-input-wrap">
                                <input type="number"
                                       class="form-control form-control-sm p-0 ps-1 text-center spaces-num-input"
                                       style="width:54px;height:22px;font-size:.7rem"
                                       min="0"
                                       max="{{ $maxEditar }}"
                                       value="{{ $day->espacios_disponibles }}"
                                       title="0 = bloquear todo · {{ $maxEditar }} = máximo posible">
                                <button class="cal-btn" style="color:#198754;border-color:#198754;padding:2px 5px"
                                        onclick="guardarDesdeBtn(this)">✓</button>
                                <button class="cal-btn" style="color:#6c757d;border-color:#6c757d;padding:2px 5px"
                                        onclick="cancelarEdicion(this)">✕</button>
                            </div>

                            {{-- Botón reservar --}}
                            <div class="cal-actions">
                                @if(!$lleno)
                                    <button class="cal-btn cal-btn-reserve" onclick="abrirModalReserva(this)">
                                        + Reservar
                                    </button>
                                @else
                                    <span class="cal-stat text-center w-100" style="color:#dc3545;font-size:.65rem">
                                    {{ $day->espacios_usados > 0 && $day->espacios_bloqueados === 0 ? 'Lleno' : 'Sin disp.' }}
                                </span>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>No hay días generados.
            </div>
        </div>
    @endforelse


    {{-- ── Modal: Nueva reservación ── --}}
    <div class="modal fade" id="modalReserva" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content overflow-hidden">

                {{-- Encabezado --}}
                <div class="modal-header" id="r_header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>Nueva reservación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                {{-- Formulario --}}
                <form id="formReserva" method="POST" action="{{ route('reservations.store') }}">
                    @csrf
                    <input type="hidden" name="tour_id" value="{{ $tour->id }}">
                    <input type="hidden" name="fecha" id="r_fecha_hidden">
                    <div class="modal-body" id="r_form_body">
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-medium">{{ $tour->nombre }}</div>
                                        <div class="text-muted small" id="r_fecha_label"></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold fs-5 lh-1" id="r_disponibles_num"></div>
                                        <div class="text-muted" style="font-size:.7rem">disponibles</div>
                                    </div>
                                </div>
                                <div class="progress mt-2" style="height:5px">
                                    <div id="r_progress_bar" class="progress-bar" style="width:0%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1" style="font-size:.7rem;color:#888">
                                    <span id="r_usados_label"></span>
                                    <span id="r_capacidad_label"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Nombre del cliente <span class="text-danger">*</span></label>
                            <input type="text" name="cliente_nombre" class="form-control" required placeholder="Nombre completo">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="cliente_email" class="form-control" required placeholder="correo@ejemplo.com">
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-medium">Cantidad de espacios <span class="text-danger">*</span></label>
                            <input type="number" name="cantidad_espacios" id="r_cantidad" class="form-control" min="1" value="1" required>
                            <div class="form-text" id="r_max_hint"></div>
                        </div>
                        <div id="r_error" class="alert alert-danger py-2 mt-2 d-none"></div>
                    </div>
                    <div class="modal-footer" id="r_form_footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" id="r_submit_btn" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i>Confirmar reservación
                        </button>
                    </div>
                </form>

                {{-- Panel de confirmación (oculto hasta que se guarda) --}}
                <div id="r_confirm_panel" style="display:none">
                    <div class="modal-body p-0">

                        {{-- Franja verde animada --}}
                        <div id="r_confirm_stripe"
                             style="background:linear-gradient(135deg,#198754,#20c76a);
                                padding:28px 24px 20px;
                                transform:translateY(-100%);
                                transition:transform .4s cubic-bezier(.22,.61,.36,1)">
                            <div class="d-flex align-items-center gap-3">
                                {{-- Círculo con check animado --}}
                                <div id="r_check_circle"
                                     style="width:52px;height:52px;border-radius:50%;
                                        background:rgba(255,255,255,.25);
                                        display:flex;align-items:center;justify-content:center;
                                        flex-shrink:0;
                                        transform:scale(0);
                                        transition:transform .35s cubic-bezier(.34,1.56,.64,1) .25s">
                                    <i class="bi bi-check-lg text-white" style="font-size:1.6rem;line-height:1"></i>
                                </div>
                                <div>
                                    <div class="text-white fw-semibold fs-5 lh-1 mb-1">¡Reservación confirmada!</div>
                                    <div id="r_confirm_fecha" class="text-white opacity-75 small"></div>
                                </div>
                            </div>
                        </div>

                        {{-- Detalles --}}
                        <div id="r_confirm_details"
                             style="padding:20px 24px 4px;
                                opacity:0;transform:translateY(10px);
                                transition:opacity .3s ease .55s, transform .3s ease .55s">

                            {{-- Datos del cliente --}}
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div style="width:38px;height:38px;border-radius:50%;background:#e8f5e9;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    <i class="bi bi-person-fill text-success" style="font-size:1rem"></i>
                                </div>
                                <div>
                                    <div class="fw-medium" id="r_c_nombre" style="font-size:.95rem"></div>
                                    <div class="text-muted small" id="r_c_email"></div>
                                </div>
                            </div>

                            <hr class="my-2">

                            {{-- Stats --}}
                            <div class="row g-2 mb-3">
                                <div class="col-4 text-center">
                                    <div class="fw-semibold fs-4 text-success lh-1" id="r_c_espacios"></div>
                                    <div class="text-muted" style="font-size:.7rem">espacios reservados</div>
                                </div>
                                <div class="col-4 text-center border-start border-end">
                                    <div class="fw-semibold fs-4 lh-1" id="r_c_disponibles_new" style="color:#0d6efd"></div>
                                    <div class="text-muted" style="font-size:.7rem">disponibles ahora</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="fw-semibold fs-4 lh-1 text-secondary" id="r_c_usados_new"></div>
                                    <div class="text-muted" style="font-size:.7rem">en total usados</div>
                                </div>
                            </div>

                            {{-- Barra actualizada --}}
                            <div class="cal-progress mb-3" style="height:6px;border-radius:3px">
                                <div id="r_c_bar" class="cal-progress-bar" style="width:0%;transition:width .6s ease .7s"></div>
                            </div>

                            {{-- ID de reservación --}}
                            <div class="text-muted d-flex align-items-center gap-2" style="font-size:.72rem">
                                <i class="bi bi-hash"></i>
                                <span id="r_c_id" class="font-monospace"></span>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top-0 pt-0 pb-3 px-4 justify-content-between">
                        <a id="r_c_ver_link" href="#" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye me-1"></i>Ver reservación
                        </a>
                        <button type="button" class="btn btn-success px-4" id="r_confirm_close_btn">
                            Cerrar
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- ── Modal: Edición en bloque ── --}}
    <div class="modal fade" id="modalBulk" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Editar disponibles en bloque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('calendars.bulk', [$tour, $calendar]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-medium">Mes</label>
                            <select name="mes" class="form-select">
                                <option value="">Todo el año</option>
                                @foreach(range(1,12) as $m)
                                    <option value="{{ $m }}" {{ $mesFiltro == $m ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create($calendar->anio, $m)->translatedFormat('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-medium">
                                Espacios disponibles deseados por día <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="disponibles_deseados" class="form-control"
                                   min="0" max="{{ $tour->capacidad_maxima }}" required>
                            <div class="form-text">
                                <strong>0</strong> bloquea el día sin afectar reservas ya hechas.
                                Máximo posible: <strong>{{ number_format($tour->capacidad_maxima) }}</strong>.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning"
                                onclick="return confirm('¿Aplicar esta disponibilidad en bloque?')">
                            <i class="bi bi-arrow-repeat me-1"></i>Aplicar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Toast --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
        <div id="toast" class="toast align-items-center text-white border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toast-msg"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        const CSRF        = '{{ csrf_token() }}';
        const RESERVE_URL = '{{ route("reservations.store") }}';
        const dayUrl      = (id) => `/tours/{{ $tour->id }}/calendars/{{ $calendar->id }}/days/${id}`;

        // ── Helpers generales ─────────────────────────────────────────
        function showToast(msg, ok = true) {
            const el = document.getElementById('toast');
            el.classList.remove('bg-success', 'bg-danger');
            el.classList.add(ok ? 'bg-success' : 'bg-danger');
            document.getElementById('toast-msg').textContent = msg;
            bootstrap.Toast.getOrCreateInstance(el, { delay: 2000 }).show();
        }
        function getCell(el)    { return el.closest('.cal-cell'); }
        function getDisplay(el) { return getCell(el).querySelector('.spaces-display'); }
        function getWrap(el)    { return getCell(el).querySelector('.spaces-input-wrap'); }

        // ── Navegación Tab ────────────────────────────────────────────
        function abrirSiguiente(currentInput) {
            const inputs = [...document.querySelectorAll('.cal-cell:not(.empty) .spaces-num-input')];
            const idx    = inputs.indexOf(currentInput);
            if (idx === -1 || idx === inputs.length - 1) return;
            const nextInput = inputs[idx + 1];
            const nextCell  = nextInput.closest('.cal-cell');
            const currWrap  = currentInput.closest('.spaces-input-wrap');
            currWrap.classList.remove('active');
            getDisplay(currWrap).classList.remove('hidden');
            abrirEdicion(nextCell.querySelector('.spaces-display'));
        }

        // ── Inline edit ───────────────────────────────────────────────
        function abrirEdicion(displayEl) {
            const cell  = displayEl.closest('.cal-cell');
            const wrap  = cell.querySelector('.spaces-input-wrap');
            const input = wrap.querySelector('.spaces-num-input');
            input.value = parseInt(cell.dataset.disponibles);
            input.max   = parseInt(cell.dataset.maxEditar);
            wrap.classList.remove('saved');
            displayEl.classList.add('hidden');
            wrap.classList.add('active');
            input.focus();
            input.select();
        }

        function cancelarEdicion(btn) {
            const wrap = getWrap(btn);
            wrap.classList.remove('active', 'saved');
            getDisplay(btn).classList.remove('hidden');
        }

        // Actualiza la celda del calendario con datos nuevos de disponibilidad
        function actualizarCelda(cell, a) {
            const usados   = a.espacios_usados;
            const capacidad = parseInt(cell.dataset.capacidad);
            cell.dataset.usados      = usados;
            cell.dataset.disponibles = a.espacios_disponibles;
            cell.dataset.bloqueados  = a.espacios_bloqueados;
            cell.dataset.maxEditar   = capacidad - usados;

            const lleno     = a.espacios_disponibles <= 0;
            const pctBarra  = capacidad > 0
                ? Math.round(((usados + a.espacios_bloqueados) / capacidad) * 100)
                : 100;
            const casilleno = !lleno && Math.round((usados / capacidad) * 100) >= 75;
            const color     = lleno ? '#dc3545' : (casilleno ? '#ffc107' : '#198754');

            cell.classList.toggle('lleno',      lleno);
            cell.classList.toggle('casi-lleno', casilleno);
            cell.classList.toggle('bloqueado',  a.espacios_bloqueados > 0 && !lleno);

            // Animar la barra
            const bar = cell.querySelector('.cal-progress-bar');
            bar.classList.remove('bar-saved');
            void bar.offsetWidth;
            bar.style.width      = Math.min(pctBarra, 100) + '%';
            bar.style.background = color;
            bar.classList.add('bar-saved');
            bar.addEventListener('animationend', () => bar.classList.remove('bar-saved'), { once: true });

            // Display
            const display   = cell.querySelector('.spaces-display');
            const bloqBadge = a.espacios_bloqueados > 0
                ? `<span class="badge-bloqueado d-block mt-1">
             <i class="bi bi-lock-fill" style="font-size:.55rem"></i>
             ${a.espacios_bloqueados} bloq.
           </span>` : '';
            display.innerHTML =
                `<strong style="color:${color}">${a.espacios_disponibles}</strong><span> disp.</span>${bloqBadge}`;

            // Botón reservar
            const actDiv = cell.querySelector('.cal-actions');
            if (lleno) {
                actDiv.innerHTML = `<span class="cal-stat text-center w-100" style="color:#dc3545;font-size:.65rem">Sin disp.</span>`;
            } else if (!actDiv.querySelector('.cal-btn-reserve')) {
                actDiv.innerHTML = `<button class="cal-btn cal-btn-reserve" onclick="abrirModalReserva(this)">+ Reservar</button>`;
            } else {
                actDiv.querySelector('.cal-btn-reserve').dataset.disponibles = a.espacios_disponibles;
            }
        }

        // ── Guardar disponibles ───────────────────────────────────────
        async function guardarDisponibles(input, { tabAfter = false } = {}) {
            const cell      = getCell(input);
            const wrap      = getWrap(input);
            const display   = getDisplay(input);
            const deseados  = parseInt(input.value);
            const id        = cell.dataset.id;
            const usados    = parseInt(cell.dataset.usados);
            const maxEditar = parseInt(cell.dataset.maxEditar);

            if (isNaN(deseados) || deseados < 0) { showToast('Valor inválido.', false); return; }
            if (deseados > maxEditar) { showToast(`Máximo: ${maxEditar} (${usados} ya reservados).`, false); return; }

            try {
                const res = await fetch(dayUrl(id), {
                    method : 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body   : JSON.stringify({ disponibles_deseados: deseados }),
                });
                if (!res.ok) throw new Error();
                const d = await res.json();

                actualizarCelda(cell, d);

                wrap.classList.add('saved');
                setTimeout(() => wrap.classList.remove('saved'), 1200);

                if (tabAfter) {
                    setTimeout(() => abrirSiguiente(input), 120);
                } else {
                    wrap.classList.remove('active');
                    display.classList.remove('hidden');
                    showToast(d.espacios_bloqueados > 0
                        ? `${d.espacios_bloqueados} bloqueados · ${d.espacios_disponibles} disponibles`
                        : 'Disponibilidad actualizada.');
                }
            } catch {
                showToast('Error al guardar.', false);
            }
        }

        function guardarDesdeBtn(btn) {
            guardarDisponibles(getWrap(btn).querySelector('.spaces-num-input'));
        }

        document.addEventListener('keydown', e => {
            if (!e.target.matches('.spaces-num-input')) return;
            const input = e.target;
            if (e.key === 'Tab')    { e.preventDefault(); guardarDisponibles(input, { tabAfter: true }); return; }
            if (e.key === 'Enter')  { guardarDisponibles(input); return; }
            if (e.key === 'Escape') { cancelarEdicion(input.closest('.spaces-input-wrap').querySelector('button:last-of-type')); }
        });

        document.addEventListener('change', e => {
            if (!e.target.matches('.spaces-num-input')) return;
            if (e.target.closest('.spaces-input-wrap').classList.contains('active')) {
                guardarDisponibles(e.target);
            }
        });

        // ── Modal reserva ─────────────────────────────────────────────
        const bsModalReserva  = new bootstrap.Modal(document.getElementById('modalReserva'));
        let   _activeCellId   = null;   // id de la celda que disparó el modal

        function abrirModalReserva(btn) {
            const cell        = getCell(btn);
            _activeCellId     = cell.dataset.id;

            const disponibles = parseInt(cell.dataset.disponibles);
            const usados      = parseInt(cell.dataset.usados);
            const bloqueados  = parseInt(cell.dataset.bloqueados);
            const capacidad   = parseInt(cell.dataset.capacidad);
            const pct         = capacidad > 0 ? Math.round(((usados + bloqueados) / capacidad) * 100) : 0;

            // Resetear a estado de formulario
            resetModalToForm();

            document.getElementById('r_fecha_hidden').value          = cell.dataset.fecha;
            document.getElementById('r_fecha_label').textContent     = cell.dataset.fechaLabel;
            document.getElementById('r_disponibles_num').textContent = disponibles;
            document.getElementById('r_usados_label').textContent    = `${usados} reservados`;
            document.getElementById('r_capacidad_label').textContent = `${capacidad} cap. total`;
            document.getElementById('r_max_hint').textContent        = `Máximo: ${disponibles} espacios disponibles`;
            document.getElementById('r_cantidad').max   = disponibles;
            document.getElementById('r_cantidad').value = 1;

            const bar = document.getElementById('r_progress_bar');
            bar.style.width = Math.min(pct, 100) + '%';
            bar.className   = 'progress-bar ' + (pct >= 100 ? 'bg-danger' : pct >= 75 ? 'bg-warning' : 'bg-success');

            document.getElementById('formReserva').querySelector('[name="cliente_nombre"]').value = '';
            document.getElementById('formReserva').querySelector('[name="cliente_email"]').value  = '';

            bsModalReserva.show();
        }

        function resetModalToForm() {
            document.getElementById('r_form_body').style.display    = '';
            document.getElementById('r_form_footer').style.display  = '';
            document.getElementById('r_header').style.display       = '';
            document.getElementById('r_confirm_panel').style.display = 'none';
            document.getElementById('r_error').classList.add('d-none');

            const btn = document.getElementById('r_submit_btn');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Confirmar reservación';

            // Resetear animaciones del panel de confirmación
            const stripe  = document.getElementById('r_confirm_stripe');
            const circle  = document.getElementById('r_check_circle');
            const details = document.getElementById('r_confirm_details');
            stripe.style.transform  = 'translateY(-100%)';
            circle.style.transform  = 'scale(0)';
            details.style.opacity   = '0';
            details.style.transform = 'translateY(10px)';
        }

        // Interceptar el submit del formulario
        document.getElementById('formReserva').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn  = document.getElementById('r_submit_btn');
            const errEl = document.getElementById('r_error');
            errEl.classList.add('d-none');

            // Estado de carga
            btn.disabled = true;
            btn.innerHTML = `
        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
        Guardando...`;

            const formData = new FormData(this);
            const body     = Object.fromEntries(formData.entries());

            try {
                const res = await fetch(RESERVE_URL, {
                    method : 'POST',
                    headers: {
                        'Content-Type' : 'application/json',
                        'X-CSRF-TOKEN' : CSRF,
                        'Accept'       : 'application/json',
                    },
                    body: JSON.stringify(body),
                });

                const data = await res.json();

                if (!res.ok || !data.ok) {
                    throw new Error(data.error || data.message || 'Error al guardar.');
                }

                // ── Mostrar panel de confirmación ────────────────────
                const r = data.reservation;
                const a = data.availability;

                // Calcular color de la barra actualizada
                const capNum    = parseInt(document.querySelector(`[data-id="${_activeCellId}"]`)?.dataset.capacidad || body.capacidad_dia || a.capacidad_dia);
                const pctNew    = capNum > 0 ? Math.round(((a.espacios_usados + a.espacios_bloqueados) / capNum) * 100) : 100;
                const colorNew  = pctNew >= 100 ? '#dc3545' : (pctNew >= 75 ? '#ffc107' : '#198754');

                // Rellenar datos del panel
                document.getElementById('r_confirm_fecha').textContent     = document.getElementById('r_fecha_label').textContent;
                document.getElementById('r_c_nombre').textContent          = r.cliente_nombre;
                document.getElementById('r_c_email').textContent           = r.cliente_email;
                document.getElementById('r_c_espacios').textContent        = r.cantidad_espacios;
                document.getElementById('r_c_disponibles_new').textContent = a.espacios_disponibles;
                document.getElementById('r_c_usados_new').textContent      = a.espacios_usados;
                document.getElementById('r_c_id').textContent              = r.id.substring(0, 8) + '…';
                document.getElementById('r_c_ver_link').href               = r.show_url;

                const cBar = document.getElementById('r_c_bar');
                cBar.style.background = colorNew;
                cBar.style.width      = '0%';   // se animará en el siguiente frame

                // Ocultar form, mostrar panel
                document.getElementById('r_form_body').style.display   = 'none';
                document.getElementById('r_form_footer').style.display = 'none';
                document.getElementById('r_header').style.display      = 'none';
                document.getElementById('r_confirm_panel').style.display = '';

                // Secuencia de animaciones
                requestAnimationFrame(() => {
                    // 1. Franja verde entra desde arriba
                    document.getElementById('r_confirm_stripe').style.transform = 'translateY(0)';

                    // 2. Círculo con check aparece con rebote
                    setTimeout(() => {
                        document.getElementById('r_check_circle').style.transform = 'scale(1)';
                    }, 250);

                    // 3. Detalles aparecen
                    setTimeout(() => {
                        const det = document.getElementById('r_confirm_details');
                        det.style.opacity   = '1';
                        det.style.transform = 'translateY(0)';
                    }, 500);

                    // 4. Barra animada
                    setTimeout(() => {
                        cBar.style.width = Math.min(pctNew, 100) + '%';
                    }, 650);
                });

                // Actualizar la celda del calendario en segundo plano
                const activeCell = document.querySelector(`.cal-cell[data-id="${_activeCellId}"]`);
                if (activeCell) actualizarCelda(activeCell, a);

            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-lg me-1"></i>Confirmar reservación';
                errEl.textContent = err.message;
                errEl.classList.remove('d-none');
            }
        });

        // Botón "Cerrar" del panel de confirmación
        document.getElementById('r_confirm_close_btn').addEventListener('click', () => {
            bsModalReserva.hide();
        });

        // Al cerrar el modal, resetear siempre
        document.getElementById('modalReserva').addEventListener('hidden.bs.modal', () => {
            resetModalToForm();
        });
    </script>
@endsection
