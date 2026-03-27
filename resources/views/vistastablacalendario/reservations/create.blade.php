@extends('layouts.app')

@section('title', 'Nueva reservación')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-semibold">Nueva reservación</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('reservations.store') }}" method="POST" id="form-reservacion">
                    @csrf

                    {{-- Tour --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Tour <span class="text-danger">*</span></label>
                        <select name="tour_id" id="tour_id"
                                class="form-select @error('tour_id') is-invalid @enderror" required>
                            <option value="">Selecciona un tour...</option>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}"
                                    {{ old('tour_id', $selectedTour?->id) === $tour->id ? 'selected' : '' }}>
                                    {{ $tour->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tour_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Fecha --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" id="fecha"
                               min="{{ now()->toDateString() }}"
                               class="form-control @error('fecha') is-invalid @enderror"
                               value="{{ old('fecha') }}" required>
                        @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Disponibilidad en tiempo real --}}
                    <div id="disponibilidad-info" class="mb-3" style="display:none">
                        <div class="card bg-light border-0">
                            <div class="card-body py-2 px-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small text-muted">Disponibilidad</span>
                                    <span id="disp-badge"></span>
                                </div>
                                <div class="progress mt-2" style="height:6px">
                                    <div id="disp-bar" class="progress-bar" style="width:0%"></div>
                                </div>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="small text-muted" id="disp-usados"></span>
                                    <span class="small fw-medium" id="disp-disponibles"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- Datos del cliente --}}
                    <div class="mb-3">
                        <label class="form-label fw-medium">Nombre del cliente <span class="text-danger">*</span></label>
                        <input type="text" name="cliente_nombre"
                               class="form-control @error('cliente_nombre') is-invalid @enderror"
                               value="{{ old('cliente_nombre') }}" required>
                        @error('cliente_nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Email del cliente <span class="text-danger">*</span></label>
                        <input type="email" name="cliente_email"
                               class="form-control @error('cliente_email') is-invalid @enderror"
                               value="{{ old('cliente_email') }}" required>
                        @error('cliente_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-medium">Cantidad de espacios <span class="text-danger">*</span></label>
                        <input type="number" name="cantidad_espacios" id="cantidad_espacios"
                               min="1" class="form-control @error('cantidad_espacios') is-invalid @enderror"
                               value="{{ old('cantidad_espacios', 1) }}" required>
                        @error('cantidad_espacios')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btn-reservar">
                            <i class="bi bi-check-lg me-1"></i>Confirmar reservación
                        </button>
                        <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Panel lateral de ayuda --}}
    <div class="col-md-4 offset-md-1">
        <div class="card border-0 shadow-sm bg-info-subtle border-info-subtle">
            <div class="card-body">
                <h6 class="fw-semibold"><i class="bi bi-info-circle me-2"></i>¿Cómo funciona?</h6>
                <ol class="small text-muted mb-0 ps-3">
                    <li class="mb-1">Selecciona el tour y la fecha deseada.</li>
                    <li class="mb-1">Verifica la disponibilidad que aparece automáticamente.</li>
                    <li class="mb-1">Ingresa los datos del cliente y la cantidad de espacios.</li>
                    <li>Confirma la reservación.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const tourSelect  = document.getElementById('tour_id');
const fechaInput  = document.getElementById('fecha');
const infoBox     = document.getElementById('disponibilidad-info');
const badge       = document.getElementById('disp-badge');
const bar         = document.getElementById('disp-bar');
const usadosEl    = document.getElementById('disp-usados');
const disponEl    = document.getElementById('disp-disponibles');
const cantInput   = document.getElementById('cantidad_espacios');

async function checkDisponibilidad() {
    const tourId = tourSelect.value;
    const fecha  = fechaInput.value;

    if (!tourId || !fecha) { infoBox.style.display = 'none'; return; }

    try {
        const res  = await fetch(`/api/disponibilidad?tour_id=${tourId}&fecha=${fecha}`);
        const data = await res.json();

        infoBox.style.display = 'block';

        if (!data.encontrado) {
            badge.innerHTML = '<span class="badge bg-secondary">Sin calendario</span>';
            bar.style.width = '0%';
            bar.className   = 'progress-bar bg-secondary';
            usadosEl.textContent   = '';
            disponEl.textContent   = 'No hay calendario para esta fecha';
            cantInput.max = 0;
            return;
        }

        const pct = data.capacidad_dia > 0
            ? Math.round((data.espacios_usados / data.capacidad_dia) * 100)
            : 100;

        const colorClass = pct >= 100 ? 'bg-danger'
                         : pct >= 75  ? 'bg-warning'
                         : 'bg-success';

        badge.innerHTML = data.disponible
            ? `<span class="badge bg-success-subtle text-success border border-success-subtle">Disponible</span>`
            : `<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Sin disponibilidad</span>`;

        bar.style.width = Math.min(pct, 100) + '%';
        bar.className   = `progress-bar ${colorClass}`;
        usadosEl.textContent  = `${data.espacios_usados} usados de ${data.capacidad_dia}`;
        disponEl.textContent  = `${data.espacios_disponibles} disponibles`;
        cantInput.max         = data.espacios_disponibles;
    } catch (e) {
        infoBox.style.display = 'none';
    }
}

tourSelect.addEventListener('change', checkDisponibilidad);
fechaInput.addEventListener('change', checkDisponibilidad);

// Verificar al cargar si ya hay valores (old input)
if (tourSelect.value && fechaInput.value) checkDisponibilidad();
</script>
@endsection
