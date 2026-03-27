<form action="{{ isset($idioma) ? route('admin.idiomas.update', $idioma) : route('admin.idiomas.store') }}"
      method="POST">
    @csrf
    @if(isset($idioma) && $idioma->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <div class="col-md-8">
            <label class="form-label">Nombre del Idioma <span class="text-danger">*</span></label>
            <input type="text" name="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ isset($idioma) ? $idioma->nombre : old('nombre') }}"
                   placeholder="Ej: Español, Inglés, Francés..."
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Código ISO</label>
            <input type="text" name="codigo"
                   class="form-control @error('codigo') is-invalid @enderror"
                   value="{{ isset($idioma) ? $idioma->codigo : old('codigo') }}"
                   placeholder="Ej: es, en, fr"
                   maxlength="10">
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Código de 2-3 letras (opcional).</div>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line align-middle me-1"></i>
            {{ isset($idioma) ? 'Actualizar Idioma' : 'Guardar Idioma' }}
        </button>
        <a href="{{ route('admin.idiomas.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
