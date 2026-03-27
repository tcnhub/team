@extends('layouts.app')

@section('title', 'Reservaciones')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold"><i class="bi bi-calendar-check me-2"></i>Reservaciones</h4>
    <a href="{{ route('reservations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nueva reservación
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cliente</th>
                    <th>Tour</th>
                    <th class="text-center">Fecha</th>
                    <th class="text-center">Espacios</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Creada</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                <tr>
                    <td>
                        <div class="fw-medium">{{ $r->cliente_nombre }}</div>
                        <div class="text-muted small">{{ $r->cliente_email }}</div>
                    </td>
                    <td>{{ $r->tour->nombre ?? '—' }}</td>
                    <td class="text-center">
                        {{ $r->availability?->fecha?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="text-center">{{ $r->cantidad_espacios }}</td>
                    <td class="text-center">
                        @if($r->estado === 'confirmada')
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Confirmada</span>
                        @elseif($r->estado === 'cancelada')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Cancelada</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pendiente</span>
                        @endif
                    </td>
                    <td class="text-center text-muted small">
                        {{ $r->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('reservations.show', $r) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No hay reservaciones registradas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $reservations->links() }}</div>
@endsection
