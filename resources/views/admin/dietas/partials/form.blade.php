<form action="{{ isset($dieta) ? route('admin.dietas.update', $dieta) : route('admin.dietas.store') }}"
      method="POST">
    @csrf
    @if(isset($dieta) && $dieta->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <div class="col-md-8">
            <label class="form-label">Nombre de la Dieta <span class="text-danger">*</span></label>
            <input type="text" name="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ isset($dieta) ? $dieta->nombre : old('nombre') }}"
                   placeholder="Ej: Vegetariana, Sin gluten, Kosher..."
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion"
                      class="form-control @error('descripcion') is-invalid @enderror"
                      rows="3"
                      placeholder="Descripción de la dieta o restricciones alimentarias...">{{ isset($dieta) ? $dieta->descripcion : old('descripcion') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line align-middle me-1"></i>
            {{ isset($dieta) ? 'Actualizar Dieta' : 'Guardar Dieta' }}
        </button>
        <a href="{{ route('admin.dietas.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
