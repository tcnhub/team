@extends('layouts.app')

@section('title', 'Nuevo tour')

@section('content')
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="{{ route('tours.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-semibold">Nuevo tour</h4>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('tours.store') }}" method="POST">
                    @csrf
                    @include('tours._form')
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg me-1"></i>Guardar
                        </button>
                        <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
