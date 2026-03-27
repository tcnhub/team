@extends('layouts.app')

@section('title', 'Tours')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-semibold"><i class="bi bi-compass me-2"></i>Tours</h4>
    <a href="{{ route('tours.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Nuevo tour
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th class="text-center">Cap. máxima</th>
                    <th class="text-center">Cap. diaria</th>
                    <th class="text-center">Calendarios</th>
                    <th class="text-center">Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($tours as $tour)
                <tr>
                    <td>
                        <a href="{{ route('tours.show', $tour) }}" class="fw-medium text-decoration-none">
                            {{ $tour->nombre }}
                        </a>
                        @if($tour->descripcion)
                            <div class="text-muted small">{{ Str::limit($tour->descripcion, 60) }}</div>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($tour->capacidad_maxima) }}</td>
                    <td class="text-center">{{ number_format($tour->capacidad_diaria) }}</td>
                    <td class="text-center">
                        <span class="badge bg-secondary">{{ $tour->calendar_years_count }}</span>
                    </td>
                    <td class="text-center">
                        @if($tour->activo)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end pe-3">
                        <a href="{{ route('tours.show', $tour) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('tours.edit', $tour) }}" class="btn btn-sm btn-outline-primary ms-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('tours.destroy', $tour) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('¿Eliminar este tour?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger ms-1">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No hay tours registrados.
                        <a href="{{ route('tours.create') }}">Crear el primero</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $tours->links('pagination::bootstrap-5') }}
</div>
@endsection
