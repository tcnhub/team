<form action="{{ isset($agente) ? route('admin.agentes.update', $agente) : route('admin.agentes.store') }}"
      method="POST">
    @csrf
    @if(isset($agente) && $agente->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <!-- Código Agente -->
        <div class="col-md-3">
            <label class="form-label">Código Agente</label>
            <input type="text" name="codigo_agente"
                   class="form-control @error('codigo_agente') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->codigo_agente : old('codigo_agente') }}"
                   placeholder="Ej: AGT-001">
            @error('codigo_agente')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Nombres -->
        <div class="col-md-4">
            <label class="form-label">Nombres <span class="text-danger">*</span></label>
            <input type="text" name="nombres"
                   class="form-control @error('nombres') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->nombres : old('nombres') }}"
                   required>
            @error('nombres')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Apellidos -->
        <div class="col-md-5">
            <label class="form-label">Apellidos <span class="text-danger">*</span></label>
            <input type="text" name="apellidos"
                   class="form-control @error('apellidos') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->apellidos : old('apellidos') }}"
                   required>
            @error('apellidos')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="col-md-5">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->email : old('email') }}"
                   required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Celular -->
        <div class="col-md-3">
            <label class="form-label">Celular <span class="text-danger">*</span></label>
            <input type="text" name="celular"
                   class="form-control @error('celular') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->celular : old('celular') }}"
                   required>
            @error('celular')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Teléfono -->
        <div class="col-md-4">
            <label class="form-label">Teléfono fijo</label>
            <input type="text" name="telefono"
                   class="form-control @error('telefono') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->telefono : old('telefono') }}">
            @error('telefono')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- DNI -->
        <div class="col-md-3">
            <label class="form-label">DNI / Documento</label>
            <input type="text" name="dni"
                   class="form-control @error('dni') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->dni : old('dni') }}">
            @error('dni')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fecha Nacimiento -->
        <div class="col-md-3">
            <label class="form-label">Fecha de Nacimiento</label>
            <input type="date" name="fecha_nacimiento"
                   class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                   value="{{ isset($agente) && $agente->fecha_nacimiento ? $agente->fecha_nacimiento->format('Y-m-d') : old('fecha_nacimiento') }}">
            @error('fecha_nacimiento')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Género -->
        <div class="col-md-3">
            <label class="form-label">Género</label>
            <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                <option value="">Seleccionar...</option>
                <option value="masculino" {{ (isset($agente) && $agente->genero == 'masculino') || old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                <option value="femenino" {{ (isset($agente) && $agente->genero == 'femenino') || old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                <option value="otro" {{ (isset($agente) && $agente->genero == 'otro') || old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
            </select>
            @error('genero')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Estado -->
        <div class="col-md-3">
            <label class="form-label">Estado <span class="text-danger">*</span></label>
            <select name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="activo" {{ (isset($agente) && $agente->estado == 'activo') || old('estado', 'activo') == 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ (isset($agente) && $agente->estado == 'inactivo') || old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                <option value="vacaciones" {{ (isset($agente) && $agente->estado == 'vacaciones') || old('estado') == 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                <option value="baja" {{ (isset($agente) && $agente->estado == 'baja') || old('estado') == 'baja' ? 'selected' : '' }}>Baja</option>
            </select>
            @error('estado')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Departamento -->
        <div class="col-md-4">
            <label class="form-label">Departamento</label>
            <input type="text" name="departamento"
                   class="form-control @error('departamento') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->departamento : old('departamento') }}"
                   placeholder="Ventas, Corporativo, etc.">
            @error('departamento')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Comisión -->
        <div class="col-md-2">
            <label class="form-label">Comisión (%)</label>
            <input type="number" name="comision_porcentaje" step="0.01" min="0" max="100"
                   class="form-control @error('comision_porcentaje') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->comision_porcentaje : old('comision_porcentaje', 0) }}">
            @error('comision_porcentaje')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Dirección -->
        <div class="col-md-6">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion"
                   class="form-control @error('direccion') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->direccion : old('direccion') }}">
            @error('direccion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Ciudad -->
        <div class="col-md-3">
            <label class="form-label">Ciudad</label>
            <input type="text" name="ciudad"
                   class="form-control @error('ciudad') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->ciudad : old('ciudad', 'Lima') }}">
            @error('ciudad')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- País -->
        <div class="col-md-3">
            <label class="form-label">País</label>
            <input type="text" name="pais"
                   class="form-control @error('pais') is-invalid @enderror"
                   value="{{ isset($agente) ? $agente->pais : old('pais', 'Perú') }}">
            @error('pais')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fechas laborales -->
        <div class="col-md-3">
            <label class="form-label">Fecha de Ingreso</label>
            <input type="date" name="fecha_ingreso"
                   class="form-control @error('fecha_ingreso') is-invalid @enderror"
                   value="{{ isset($agente) && $agente->fecha_ingreso ? $agente->fecha_ingreso->format('Y-m-d') : old('fecha_ingreso') }}">
            @error('fecha_ingreso')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha de Salida</label>
            <input type="date" name="fecha_salida"
                   class="form-control @error('fecha_salida') is-invalid @enderror"
                   value="{{ isset($agente) && $agente->fecha_salida ? $agente->fecha_salida->format('Y-m-d') : old('fecha_salida') }}">
            @error('fecha_salida')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Notas -->
        <div class="col-12">
            <label class="form-label">Notas</label>
            <textarea name="notas" class="form-control @error('notas') is-invalid @enderror" rows="3">{{ isset($agente) ? $agente->notas : old('notas') }}</textarea>
            @error('notas')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line align-middle me-1"></i>
            {{ isset($agente) ? 'Actualizar Agente' : 'Guardar Agente' }}
        </button>
        <a href="{{ route('admin.agentes.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
