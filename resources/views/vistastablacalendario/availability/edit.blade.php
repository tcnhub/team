@extends('layouts.app')

@section('title', 'Editar día')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('calendars.show', [$tour, $calendar]) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-semibold">
        Editar día —
        {{ $availability->fecha->translatedFormat('l d \d\e F \d\e Y') }}
    </h4>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col">
                        <div class="text-muted small">Usados</div>
                        <div class="fs-4 fw-semibold">{{ $availability->espacios_usados }}</div>
                    </div>
                    <div class="col">
                        <div class="text-muted small">Disponibles</div>
                        <div class="fs-4 fw-semibold">{{ $availability->espacios_disponibles }}</div>
                    </div>
                    <div class="col">
                        <div class="text-muted small">Capacidad</div>
                        <div class="fs-4 fw-semibold">{{ $availability->capacidad_dia }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('calendars.days.update', [$tour, $calendar, $availability]) }}"
                      method="POST">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-medium">
                            Capacidad para este día <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="capacidad_dia" min="{{ $availability->espacios_usados }}"
                               max="{{ $tour->capacidad_maxima }}"
                               class="form-control @error('capacidad_dia') is-invalid @enderror"
                               value="{{ old('capacidad_dia', $availability->capacidad_dia) }}" required>
                        <div class="form-text">
                            Mínimo: {{ $availability->espacios_usados }} (ya reservados).
                            Máximo: {{ number_format($tour->capacidad_maxima) }}.
                        </div>
                        @error('capacidad_dia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Guardar
                        </button>
                        <a href="{{ route('calendars.show', [$tour, $calendar]) }}"
                           class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
