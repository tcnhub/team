@extends('layouts.app')

@section('title', $tour->nombre)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tours.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h4 class="mb-0 fw-semibold">{{ $tour->nombre }}</h4>
            @if($tour->activo)
                <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
            @else
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tours.edit', $tour) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>Editar
            </a>
            <a href="{{ route('calendars.create', $tour) }}" class="btn btn-primary">
                <i class="bi bi-calendar-plus me-1"></i>Agregar año
            </a>
        </div>
    </div>

    {{-- Info del tour --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small mb-1">Descripción</div>
                    <p class="mb-0">{{ $tour->descripcion ?? '—' }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Capacidad máxima</div>
                    <div class="fs-2 fw-semibold">{{ number_format($tour->capacidad_maxima) }}</div>
                    <div class="text-muted small">espacios</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-muted small mb-1">Capacidad diaria por defecto</div>
                    <div class="fs-2 fw-semibold">{{ number_format($tour->capacidad_diaria) }}</div>
                    <div class="text-muted small">espacios / día</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendarios --}}
    <h5 class="fw-semibold mb-3"><i class="bi bi-calendar3 me-2"></i>Calendarios por año</h5>

    @if($tour->calendarYears->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                No hay calendarios generados.
                <a href="{{ route('calendars.create', $tour) }}">Agregar el primero</a>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($tour->calendarYears->sortByDesc('anio') as $calendar)
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fs-4 fw-semibold">{{ $calendar->anio }}</div>
                                    <div class="text-muted small">
                                        {{ $calendar->total_dias }} días
                                        @if($calendar->es_bisiesto)
                                            <span class="badge bg-info-subtle text-info border border-info-subtle ms-1">Bisiesto</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    @if($calendar->capacidad_anio)
                                        <div class="fw-medium">{{ number_format($calendar->capacidad_anio) }}</div>
                                        <div class="text-muted small">esp/día</div>
                                    @else
                                        <div class="text-muted small">Default del tour</div>
                                    @endif
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('calendars.show', [$tour, $calendar]) }}"
                                   class="btn btn-sm btn-outline-primary flex-fill">
                                    <i class="bi bi-eye me-1"></i>Ver días
                                </a>
                                <form action="{{ route('calendars.destroy', [$tour, $calendar]) }}"
                                      method="POST" onsubmit="return confirm('¿Eliminar calendario {{ $calendar->anio }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
