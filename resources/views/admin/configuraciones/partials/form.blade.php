<form action="{{ isset($configuracion) ? route('admin.configuraciones.update', $configuracion) : route('admin.configuraciones.store') }}" method="POST">
    @csrf
    @if(isset($configuracion) && $configuracion->exists)
        @method('PUT')
    @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $configuracion->nombre ?? '') }}" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="form-label">Valor</label>
            <input type="text" name="valor" class="form-control @error('valor') is-invalid @enderror" value="{{ old('valor', $configuracion->valor ?? '') }}">
            @error('valor')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> {{ isset($configuracion) ? 'Actualizar' : 'Guardar' }}</button>
        <a href="{{ route('admin.configuraciones.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
