<form action="{{ isset($categoria) ? route('admin.categorias.update', $categoria) : route('admin.categorias.store') }}"
      method="POST">
    @csrf
    @if(isset($categoria) && $categoria->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <div class="col-md-6">
            <label class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
            <input type="text" name="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ isset($categoria) ? $categoria->nombre : old('nombre') }}"
                   placeholder="Ej: Cultural, Aventura, Eco, Luxury..."
                   required>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Color (hex)</label>
            <div class="input-group">
                <input type="color" name="color"
                       class="form-control form-control-color @error('color') is-invalid @enderror"
                       value="{{ isset($categoria) && $categoria->color ? $categoria->color : (old('color') ?? '#3490dc') }}"
                       title="Elige un color">
                <input type="text" class="form-control form-control-sm"
                       value="{{ isset($categoria) ? $categoria->color : old('color', '#3490dc') }}"
                       readonly id="colorDisplay">
            </div>
            @error('color')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Ícono (clase CSS)</label>
            <input type="text" name="icono"
                   class="form-control @error('icono') is-invalid @enderror"
                   value="{{ isset($categoria) ? $categoria->icono : old('icono') }}"
                   placeholder="Ej: ri-compass-line">
            @error('icono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Clase de RemixIcon o similar.</div>
        </div>

        <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion"
                      class="form-control @error('descripcion') is-invalid @enderror"
                      rows="3"
                      placeholder="Descripción de la categoría...">{{ isset($categoria) ? $categoria->descripcion : old('descripcion') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <div class="form-check form-switch mt-2">
                <input type="checkbox" name="activo" id="activo" class="form-check-input"
                       {{ (isset($categoria) && $categoria->activo) || (!isset($categoria) && old('activo', true)) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Categoría Activa</label>
            </div>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line align-middle me-1"></i>
            {{ isset($categoria) ? 'Actualizar Categoría' : 'Guardar Categoría' }}
        </button>
        <a href="{{ route('admin.categorias.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>

<script>
    document.querySelector('input[type="color"]').addEventListener('input', function() {
        document.getElementById('colorDisplay').value = this.value;
    });
</script>
