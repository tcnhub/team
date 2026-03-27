<div class="mb-3">
    <label class="form-label fw-medium">Nombre <span class="text-danger">*</span></label>
    <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
           value="{{ old('nombre', $tour->nombre ?? '') }}" required>
    @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label fw-medium">Descripción</label>
    <textarea name="descripcion" rows="3"
              class="form-control @error('descripcion') is-invalid @enderror">{{ old('descripcion', $tour->descripcion ?? '') }}</textarea>
    @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="row">
    <div class="col-6">
        <div class="mb-3">
            <label class="form-label fw-medium">Capacidad máxima <span class="text-danger">*</span></label>
            <input type="number" name="capacidad_maxima" min="1"
                   class="form-control @error('capacidad_maxima') is-invalid @enderror"
                   value="{{ old('capacidad_maxima', $tour->capacidad_maxima ?? 300) }}" required>
            <div class="form-text">Techo absoluto del tour.</div>
            @error('capacidad_maxima')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-6">
        <div class="mb-3">
            <label class="form-label fw-medium">Capacidad diaria <span class="text-danger">*</span></label>
            <input type="number" name="capacidad_diaria" min="1"
                   class="form-control @error('capacidad_diaria') is-invalid @enderror"
                   value="{{ old('capacidad_diaria', $tour->capacidad_diaria ?? 300) }}" required>
            <div class="form-text">Default al generar días.</div>
            @error('capacidad_diaria')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" name="activo" id="activo" value="1"
        {{ old('activo', $tour->activo ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="activo">Tour activo</label>
</div>
