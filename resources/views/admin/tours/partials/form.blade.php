<form action="{{ route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="row g-3">

        <!-- Código del Tour -->
        <div class="col-md-3">
            <label class="form-label">Código del Tour <span class="text-danger">*</span></label>
            <input type="text" name="codigo_tour" class="form-control @error('codigo_tour') is-invalid @enderror"
                   value="{{ old('codigo_tour') }}" required>
            @error('codigo_tour')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nombre del Tour -->
        <div class="col-md-9">
            <label class="form-label">Nombre del Tour <span class="text-danger">*</span></label>
            <input type="text" name="nombre_tour" class="form-control @error('nombre_tour') is-invalid @enderror"
                   value="{{ old('nombre_tour') }}" required>
            @error('nombre_tour')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Duración -->
        <div class="col-md-3">
            <label class="form-label">Días</label>
            <input type="number" name="duracion_dias" class="form-control" value="{{ old('duracion_dias') }}" min="1">
        </div>
        <div class="col-md-3">
            <label class="form-label">Noches</label>
            <input type="number" name="duracion_noches" class="form-control" value="{{ old('duracion_noches') }}" min="0">
        </div>

        <!-- Precio Base -->
        <div class="col-md-3">
            <label class="form-label">Precio Base (USD)</label>
            <input type="number" step="0.01" name="precio_base" class="form-control" value="{{ old('precio_base') }}">
        </div>

        <!-- Dificultad -->
        <div class="col-md-3">
            <label class="form-label">Nivel de Dificultad</label>
            <select name="nivel_dificultad" class="form-select">
                <option value="">Seleccionar...</option>
                <option value="Fácil" {{ old('nivel_dificultad') == 'Fácil' ? 'selected' : '' }}>Fácil</option>
                <option value="Moderado" {{ old('nivel_dificultad') == 'Moderado' ? 'selected' : '' }}>Moderado</option>
                <option value="Difícil" {{ old('nivel_dificultad') == 'Difícil' ? 'selected' : '' }}>Difícil</option>
                <option value="Extremo" {{ old('nivel_dificultad') == 'Extremo' ? 'selected' : '' }}>Extremo</option>
            </select>
        </div>

        <!-- Salida y Destino -->
        <div class="col-md-6">
            <label class="form-label">Salida desde</label>
            <input type="text" name="salida_desde" class="form-control" value="{{ old('salida_desde') }}" placeholder="Cusco / Lima">
        </div>
        <div class="col-md-6">
            <label class="form-label">Destino Principal</label>
            <input type="text" name="destino_principal" class="form-control" value="{{ old('destino_principal') }}">
        </div>

        <!-- Descripciones -->
        <div class="col-12">
            <label class="form-label">Descripción Corta</label>
            <textarea name="descripcion_corta" class="form-control" rows="2">{{ old('descripcion_corta') }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Descripción Larga</label>
            <textarea name="descripcion_larga" class="form-control" rows="5">{{ old('descripcion_larga') }}</textarea>
        </div>

        <!-- Incluye / No incluye -->
        <div class="col-md-6">
            <label class="form-label">Incluye</label>
            <textarea name="incluye" class="form-control" rows="4">{{ old('incluye') }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">No Incluye</label>
            <textarea name="no_incluye" class="form-control" rows="4">{{ old('no_incluye') }}</textarea>
        </div>

        <!-- Estado y Destacado -->
        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="Activo" selected>Activo</option>
                <option value="Inactivo">Inactivo</option>
                <option value="Agotado">Agotado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input type="checkbox" name="destacado" id="destacado" class="form-check-input" {{ old('destacado') ? 'checked' : '' }}>
                <label class="form-check-label" for="destacado">Tour Destacado</label>
            </div>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> Guardar Tour
        </button>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
