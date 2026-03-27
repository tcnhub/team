@include('layouts.main')

<head>
    <?php includeFileWithVariables('layouts/title-meta.php', ['title' => $tour->nombre_tour . ' — ' . $calendar->anio]); ?>
    @include('layouts.head-css')
    <style>
        /* ── Grilla de calendario ── */
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
        .spaces-input-wrap .spaces-num-input {
            transition: border-color .25s, box-shadow .25s;
        }
        .spaces-input-wrap.saved .spaces-num-input {
            border-color: #198754 !important;
            box-shadow: 0 0 0 2px rgba(25,135,84,.3) !important;
        }
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
</head>

<body>
<div id="layout-wrapper">
    @include('layouts.menu')

    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="ri-check-line me-1"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="ri-error-warning-line me-1"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('admin.tours.show', $tour) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="ri-arrow-left-line"></i>
                        </a>
                        <h4 class="mb-0 fw-semibold">
                            {{ $tour->nombre_tour }}
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
                                        {{ \Carbon\Carbon::create($calendar->anio, $m)->locale('es')->monthName }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                        <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalBulk">
                            <i class="ri-pencil-square-line me-1"></i>Editar en bloque
                        </button>
                    </div>
                </div>

                <!-- Leyenda -->
                <div class="d-flex gap-3 mb-3 flex-wrap align-items-center" style="font-size:.75rem;color:#555">
                    <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#f0fdf4;border:1px solid #86efac"></span>Disponible</span>
                    <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#fffbf0;border:1px solid #ffe08a"></span>Casi lleno (&gt;75%)</span>
                    <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#fff5f5;border:1px solid #fcc"></span>Sin disponibilidad</span>
                    <span><span class="d-inline-block rounded me-1" style="width:12px;height:12px;background:#f8f9fa;border:1px solid #dee2e6"></span>Bloqueado</span>
                    <span class="ms-auto text-muted">
                        <i class="ri-pencil-line me-1"></i>Clic en disponibles para editar · Tab pasa al día siguiente
                    </span>
                </div>

                @php
                    $nombresMeses = [1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',
                                     7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre'];
                    $diasSemana   = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
                    $today        = \Carbon\Carbon::today()->toDateString();
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
                                         data-tour="{{ $tour->id }}"
                                         data-fecha="{{ $day->fecha->toDateString() }}"
                                         data-fecha-label="{{ $day->fecha->locale('es')->isoFormat('D [de] MMMM') }}"
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

                                        <!-- Disponibles: clicable para editar -->
                                        <div class="spaces-display cal-stat"
                                             title="Clic para cambiar disponibles"
                                             onclick="abrirEdicion(this)">
                                            <strong style="color:{{ $barColor }}">{{ $day->espacios_disponibles }}</strong>
                                            <span> disp.</span>
                                            @if($bloqManual)
                                                <span class="badge-bloqueado d-block mt-1">
                                                    <i class="ri-lock-line" style="font-size:.55rem"></i>
                                                    {{ $day->espacios_bloqueados }} bloq.
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Input inline -->
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

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center text-muted py-5">
                            <i class="ri-calendar-x-line fs-3 d-block mb-2"></i>No hay días generados.
                        </div>
                    </div>
                @endforelse

                <!-- Modal: Edición en bloque -->
                <div class="modal fade" id="modalBulk" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="ri-pencil-square-line me-2"></i>Editar disponibles en bloque
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.tours.calendar.bulk', [$tour, $calendar]) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Mes</label>
                                        <select name="mes" class="form-select" required>
                                            @foreach(range(1,12) as $m)
                                                <option value="{{ $m }}" {{ $mesFiltro == $m ? 'selected' : '' }}>
                                                    {{ $nombresMeses[$m] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <label class="form-label fw-medium">
                                            Espacios bloqueados por día <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" name="espacios_bloqueados" class="form-control"
                                               min="0" max="{{ $tour->capacidad_maxima }}" required>
                                        <div class="form-text">
                                            <strong>0</strong> = sin bloqueos. Máximo: <strong>{{ number_format($tour->capacidad_maxima) }}</strong>.
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-warning"
                                            onclick="return confirm('¿Aplicar esta disponibilidad en bloque?')">
                                        <i class="ri-arrow-go-back-line me-1"></i>Aplicar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Toast -->
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
                    <div id="toast" class="toast align-items-center text-white border-0" role="alert">
                        <div class="d-flex">
                            <div class="toast-body" id="toast-msg"></div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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

<script>
    const CSRF    = '{{ csrf_token() }}';
    const TOUR_ID = {{ $tour->id }};

    // URL de actualización AJAX: PATCH /admin/tours/{tour}/availability/{availability}
    const dayUrl = (availabilityId) =>
        `/admin/tours/${TOUR_ID}/availability/${availabilityId}`;

    // ── Helpers ───────────────────────────────────────────────────────
    function showToast(msg, ok = true) {
        const el = document.getElementById('toast');
        el.classList.remove('bg-success', 'bg-danger');
        el.classList.add(ok ? 'bg-success' : 'bg-danger');
        document.getElementById('toast-msg').textContent = msg;
        bootstrap.Toast.getOrCreateInstance(el, { delay: 2200 }).show();
    }
    function getCell(el)    { return el.closest('.cal-cell'); }
    function getDisplay(el) { return getCell(el).querySelector('.spaces-display'); }
    function getWrap(el)    { return getCell(el).querySelector('.spaces-input-wrap'); }

    // ── Navegación Tab ─────────────────────────────────────────────────
    function abrirSiguiente(currentInput) {
        const inputs = [...document.querySelectorAll('.cal-cell:not(.empty) .spaces-num-input')];
        const idx    = inputs.indexOf(currentInput);
        if (idx === -1 || idx === inputs.length - 1) return;
        const nextInput = inputs[idx + 1];
        const currWrap  = currentInput.closest('.spaces-input-wrap');
        currWrap.classList.remove('active');
        getDisplay(currWrap).classList.remove('hidden');
        abrirEdicion(nextInput.closest('.cal-cell').querySelector('.spaces-display'));
    }

    // ── Inline edit ────────────────────────────────────────────────────
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

    function actualizarCelda(cell, a) {
        const usados    = a.espacios_usados;
        const capacidad = parseInt(cell.dataset.capacidad);
        cell.dataset.usados      = usados;
        cell.dataset.disponibles = a.espacios_disponibles;
        cell.dataset.bloqueados  = a.espacios_bloqueados;
        cell.dataset.maxEditar   = capacidad - usados;

        const lleno     = a.espacios_disponibles <= 0;
        const pctBarra  = capacidad > 0
            ? Math.round(((usados + a.espacios_bloqueados) / capacidad) * 100) : 100;
        const casilleno = !lleno && Math.round((usados / capacidad) * 100) >= 75;
        const color     = lleno ? '#dc3545' : (casilleno ? '#ffc107' : '#198754');

        cell.classList.toggle('lleno',      lleno);
        cell.classList.toggle('casi-lleno', casilleno);
        cell.classList.toggle('bloqueado',  a.espacios_bloqueados > 0 && !lleno);

        const bar = cell.querySelector('.cal-progress-bar');
        bar.classList.remove('bar-saved');
        void bar.offsetWidth;
        bar.style.width      = Math.min(pctBarra, 100) + '%';
        bar.style.background = color;
        bar.classList.add('bar-saved');
        bar.addEventListener('animationend', () => bar.classList.remove('bar-saved'), { once: true });

        const display   = cell.querySelector('.spaces-display');
        const bloqBadge = a.espacios_bloqueados > 0
            ? `<span class="badge-bloqueado d-block mt-1">
                <i class="ri-lock-line" style="font-size:.55rem"></i>
                ${a.espacios_bloqueados} bloq.
               </span>` : '';
        display.innerHTML =
            `<strong style="color:${color}">${a.espacios_disponibles}</strong><span> disp.</span>${bloqBadge}`;
    }

    // ── Guardar disponibles ────────────────────────────────────────────
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
                method : 'PATCH',
                headers: {
                    'Content-Type'  : 'application/json',
                    'X-CSRF-TOKEN'  : CSRF,
                    'Accept'        : 'application/json',
                },
                body: JSON.stringify({ disponibles_deseados: deseados }),
            });
            if (!res.ok) throw new Error(await res.text());
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
        } catch (err) {
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
</script>
</body>
</html>
