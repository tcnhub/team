<form action="{{ isset($addon) ? route('admin.addons.update', $addon) : route('admin.addons.store') }}" method="POST">
    @csrf
    @if(isset($addon) && $addon->exists)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ old('nombre', $addon->nombre ?? '') }}" required>
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Monto (USD) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" min="0" name="monto" class="form-control @error('monto') is-invalid @enderror"
                   value="{{ old('monto', $addon->monto ?? '') }}" required>
            @error('monto')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control @error('descripcion') is-invalid @enderror" rows="4">{{ old('descripcion', $addon->descripcion ?? '') }}</textarea>
            @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> {{ isset($addon) ? 'Actualizar Addon' : 'Guardar Addon' }}
        </button>
        <a href="{{ route('admin.addons.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
