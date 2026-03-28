<form action="{{ isset($tour) ? route('admin.tours.update', $tour) : route('admin.tours.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($tour) && $tour->exists)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Código del Tour <span class="text-danger">*</span></label>
            <input type="text" name="codigo_tour" class="form-control @error('codigo_tour') is-invalid @enderror"
                   value="{{ old('codigo_tour', $tour->codigo_tour ?? '') }}" required>
            @error('codigo_tour')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-9">
            <label class="form-label">Nombre del Tour <span class="text-danger">*</span></label>
            <input type="text" name="nombre_tour" class="form-control @error('nombre_tour') is-invalid @enderror"
                   value="{{ old('nombre_tour', $tour->nombre_tour ?? '') }}" required>
            @error('nombre_tour')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Días</label>
            <input type="number" name="duracion_dias" class="form-control"
                   value="{{ old('duracion_dias', $tour->duracion_dias ?? '') }}" min="1">
        </div>

        <div class="col-md-3">
            <label class="form-label">Noches</label>
            <input type="number" name="duracion_noches" class="form-control"
                   value="{{ old('duracion_noches', $tour->duracion_noches ?? '') }}" min="0">
        </div>

        <div class="col-md-3">
            <label class="form-label">Precio Base (USD)</label>
            <input type="number" step="0.01" name="precio_base" class="form-control"
                   value="{{ old('precio_base', $tour->precio_base ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Nivel de Dificultad</label>
            <select name="nivel_dificultad" class="form-select">
                <option value="">Seleccionar...</option>
                @foreach(['FÃ¡cil', 'Moderado', 'DifÃ­cil', 'Extremo'] as $nivel)
                    <option value="{{ $nivel }}" {{ old('nivel_dificultad', $tour->nivel_dificultad ?? '') === $nivel ? 'selected' : '' }}>
                        {{ $nivel }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Salida desde</label>
            <input type="text" name="salida_desde" class="form-control"
                   value="{{ old('salida_desde', $tour->salida_desde ?? '') }}" placeholder="Cusco / Lima">
        </div>

        <div class="col-md-6">
            <label class="form-label">Destino Principal</label>
            <input type="text" name="destino_principal" class="form-control"
                   value="{{ old('destino_principal', $tour->destino_principal ?? '') }}">
        </div>

        <div class="col-md-3">
            <label class="form-label">Mínimo Personas</label>
            <input type="number" name="min_personas" class="form-control"
                   value="{{ old('min_personas', $tour->min_personas ?? '') }}" min="1">
        </div>

        <div class="col-md-3">
            <label class="form-label">Máximo Personas</label>
            <input type="number" name="max_personas" class="form-control"
                   value="{{ old('max_personas', $tour->max_personas ?? '') }}" min="1">
        </div>

        <div class="col-md-6">
            <label class="form-label">Categorías</label>
            <select name="categorias[]" class="form-select" multiple>
                @php
                    $seleccionadas = collect(old('categorias', isset($tour) ? $tour->categorias->pluck('id')->all() : []))->map(fn($id) => (int) $id)->all();
                @endphp
                @foreach($categorias ?? [] as $categoria)
                    <option value="{{ $categoria->id }}" {{ in_array($categoria->id, $seleccionadas, true) ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Addons Disponibles</label>
            <select name="addons[]" class="form-select" multiple>
                @php
                    $addonsSeleccionados = collect(old('addons', isset($tour) ? $tour->addons->pluck('id')->all() : []))->map(fn($id) => (int) $id)->all();
                @endphp
                @foreach($addons ?? [] as $addon)
                    <option value="{{ $addon->id }}" {{ in_array($addon->id, $addonsSeleccionados, true) ? 'selected' : '' }}>
                        {{ $addon->nombre }} · USD {{ number_format($addon->monto, 2) }}
                    </option>
                @endforeach
            </select>
            <div class="form-text">Estos addons serán los disponibles al registrar reservas de este tour.</div>
        </div>

        <div class="col-12">
            <label class="form-label">Descripción Corta</label>
            <textarea name="descripcion_corta" class="form-control" rows="2">{{ old('descripcion_corta', $tour->descripcion_corta ?? '') }}</textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Descripción Larga</label>
            <textarea name="descripcion_larga" class="form-control" rows="5">{{ old('descripcion_larga', $tour->descripcion_larga ?? '') }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">Incluye</label>
            <textarea name="incluye" class="form-control" rows="4">{{ old('incluye', $tour->incluye ?? '') }}</textarea>
        </div>

        <div class="col-md-6">
            <label class="form-label">No Incluye</label>
            <textarea name="no_incluye" class="form-control" rows="4">{{ old('no_incluye', $tour->no_incluye ?? '') }}</textarea>
        </div>

        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="estado" value="0">
                <input type="checkbox" name="estado" id="estado" class="form-check-input" value="1"
                       {{ old('estado', $tour->estado ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="estado">Tour activo</label>
            </div>
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check form-switch">
                <input type="hidden" name="destacado" value="0">
                <input type="checkbox" name="destacado" id="destacado" class="form-check-input" value="1"
                       {{ old('destacado', $tour->destacado ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="destacado">Tour destacado</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i> {{ isset($tour) ? 'Actualizar Tour' : 'Guardar Tour' }}
        </button>
        <a href="{{ route('admin.tours.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
