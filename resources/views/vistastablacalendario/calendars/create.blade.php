@extends('layouts.app')

@section('title', 'Agregar año')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('tours.show', $tour) }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-semibold">Agregar año — {{ $tour->nombre }}</h4>
</div>

<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('calendars.store', $tour) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-medium">Año <span class="text-danger">*</span></label>
                        <input type="number" name="anio" min="2020" max="2100"
                               class="form-control @error('anio') is-invalid @enderror"
                               value="{{ old('anio', now()->year) }}" required>
                        @if($aniosExistentes)
                            <div class="form-text">
                                Años ya generados:
                                @foreach($aniosExistentes as $a)
                                    <span class="badge bg-secondary">{{ $a }}</span>
                                @endforeach
                            </div>
                        @endif
                        @error('anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-medium">Capacidad por día para este año</label>
                        <input type="number" name="capacidad_anio" min="1" max="{{ $tour->capacidad_maxima }}"
                               class="form-control @error('capacidad_anio') is-invalid @enderror"
                               value="{{ old('capacidad_anio') }}" placeholder="{{ $tour->capacidad_diaria }}">
                        <div class="form-text">
                            Déjalo vacío para usar el default del tour
                            ({{ number_format($tour->capacidad_diaria) }} espacios/día).
                            Máximo permitido: {{ number_format($tour->capacidad_maxima) }}.
                        </div>
                        @error('capacidad_anio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        Se generarán automáticamente todos los días del año,
                        incluyendo el 29 de febrero si es bisiesto.
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-calendar-plus me-1"></i>Generar calendario
                        </button>
                        <a href="{{ route('tours.show', $tour) }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
