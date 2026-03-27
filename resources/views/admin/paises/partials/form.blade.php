<form action="{{ isset($pais) ? route('admin.paises.update', $pais) : route('admin.paises.store') }}"
      method="POST">
    @csrf
    @if(isset($pais) && $pais->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <div class="col-md-8">
            <label class="form-label">Nombre del País <span class="text-danger">*</span></label>
            <input type="text" name="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ isset($pais) ? $pais->nombre : old('nombre') }}"
                   placeholder="Ej: Perú, Estados Unidos, España..."
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Código ISO</label>
            <input type="text" name="codigo_iso"
                   class="form-control @error('codigo_iso') is-invalid @enderror"
                   value="{{ isset($pais) ? $pais->codigo_iso : old('codigo_iso') }}"
                   placeholder="Ej: PE, US, ES"
                   maxlength="3">
            @error('codigo_iso')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Código ISO de 2-3 letras (opcional).</div>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line align-middle me-1"></i>
            {{ isset($pais) ? 'Actualizar País' : 'Guardar País' }}
        </button>
        <a href="{{ route('admin.paises.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
