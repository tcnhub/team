@extends('layouts.app')

@section('title', 'Reservación')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-semibold">Detalle de reservación</h4>
</div>

<div class="row g-4">

    {{-- Tarjeta principal --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <span class="fw-medium"><i class="bi bi-ticket-perforated me-2"></i>Reservación</span>
                @if($reservation->estado === 'confirmada')
                    <span class="badge bg-success-subtle text-success border border-success-subtle">Confirmada</span>
                @elseif($reservation->estado === 'cancelada')
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Cancelada</span>
                @else
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pendiente</span>
                @endif
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">ID</dt>
                    <dd class="col-sm-8">
                        <code class="small">{{ $reservation->id }}</code>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Tour</dt>
                    <dd class="col-sm-8">
                        <a href="{{ route('tours.show', $reservation->tour) }}" class="text-decoration-none fw-medium">
                            {{ $reservation->tour->nombre }}
                        </a>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Fecha</dt>
                    <dd class="col-sm-8">
                        {{ $reservation->availability->fecha->translatedFormat('l d \d\e F \d\e Y') }}
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Espacios</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6">
                            {{ $reservation->cantidad_espacios }}
                        </span>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal">Creada</dt>
                    <dd class="col-sm-8">
                        {{ $reservation->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Datos del cliente --}}
    <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent">
                <span class="fw-medium"><i class="bi bi-person me-2"></i>Cliente</span>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal">Nombre</dt>
                    <dd class="col-sm-8 fw-medium">{{ $reservation->cliente_nombre }}</dd>

                    <dt class="col-sm-4 text-muted fw-normal">Email</dt>
                    <dd class="col-sm-8">
                        <a href="mailto:{{ $reservation->cliente_email }}" class="text-decoration-none">
                            {{ $reservation->cliente_email }}
                        </a>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- Disponibilidad del día --}}
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent">
                <span class="fw-medium"><i class="bi bi-bar-chart me-2"></i>Disponibilidad del día</span>
            </div>
            <div class="card-body">
                @php
                    $day = $reservation->availability;
                    $pct = $day->capacidad_dia > 0
                         ? round(($day->espacios_usados / $day->capacidad_dia) * 100)
                         : 100;
                    $colorClass = $pct >= 100 ? 'bg-danger' : ($pct >= 75 ? 'bg-warning' : 'bg-success');
                @endphp
                <div class="d-flex justify-content-between mb-1">
                    <span class="small text-muted">{{ $day->espacios_usados }} usados</span>
                    <span class="small fw-medium">{{ $day->espacios_disponibles }} disponibles</span>
                </div>
                <div class="progress mb-2" style="height:8px">
                    <div class="progress-bar {{ $colorClass }}" style="width:{{ min($pct,100) }}%"></div>
                </div>
                <div class="text-muted small text-end">
                    Capacidad del día: {{ number_format($day->capacidad_dia) }} espacios
                </div>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    @if($reservation->estado === 'confirmada')
    <div class="col-12">
        <div class="card border-0 shadow-sm border-danger-subtle">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="fw-medium">Cancelar reservación</div>
                    <div class="text-muted small">
                        Se devolverán {{ $reservation->cantidad_espacios }} espacio(s) al día.
                    </div>
                </div>
                <form action="{{ route('reservations.cancel', $reservation) }}" method="POST"
                      onsubmit="return confirm('¿Cancelar esta reservación?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle me-1"></i>Cancelar reservación
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
