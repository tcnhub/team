@extends('layouts.app')

@section('title', 'Editar tour')

@section('content')
    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="{{ route('tours.show', $tour) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="mb-0 fw-semibold">Editar: {{ $tour->nombre }}</h4>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form action="{{ route('tours.update', $tour) }}" method="POST">
                        @csrf @method('PUT')
                        @include('tours._form')
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Actualizar
                            </button>
                            <a href="{{ route('tours.show', $tour) }}" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
