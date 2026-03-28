<form action="{{ isset($cliente) ? route('admin.clientes.update', $cliente) : route('admin.clientes.store') }}"
      method="POST">

    @csrf
    @if(isset($cliente) && $cliente->exists)
        @method('PUT')
    @endif

    <div class="row g-3">

        <!-- Nombre y Apellido -->
        <div class="col-md-6">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre"
                   class="form-control @error('nombre') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->nombre : old('nombre') }}"
                   required>
            @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Apellido <span class="text-danger">*</span></label>
            <input type="text" name="apellido"
                   class="form-control @error('apellido') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->apellido : old('apellido') }}"
                   required>
            @error('apellido')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tipo y Número de Documento -->
        <div class="col-md-3">
            <label class="form-label">Tipo de Documento <span class="text-danger">*</span></label>
            <select name="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror" required>
                <option value="">Seleccionar...</option>
                <option value="passport" {{ (isset($cliente) && $cliente->tipo_documento == 'passport') || old('tipo_documento') == 'passport' ? 'selected' : '' }}>Passport</option>
                <option value="dni" {{ (isset($cliente) && $cliente->tipo_documento == 'dni') || old('tipo_documento') == 'dni' ? 'selected' : '' }}>DNI</option>
                <option value="id" {{ (isset($cliente) && $cliente->tipo_documento == 'id') || old('tipo_documento') == 'id' ? 'selected' : '' }}>ID / Otros</option>
            </select>
            @error('tipo_documento')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Número de Documento <span class="text-danger">*</span></label>
            <input type="text" name="numero_documento"
                   class="form-control @error('numero_documento') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->numero_documento : old('numero_documento') }}"
                   required>
            @error('numero_documento')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Resto de campos (mantengo el mismo patrón) -->
        <div class="col-md-4">
            <label class="form-label">País</label>
            <select name="pais_id" class="form-select @error('pais_id') is-invalid @enderror">
                <option value="">Seleccionar país...</option>
                @foreach($paises ?? [] as $pais)
                    <option value="{{ $pais->id }}"
                        {{ (isset($cliente) && $cliente->pais_id == $pais->id) || old('pais_id') == $pais->id ? 'selected' : '' }}>
                        {{ $pais->nombre }}
                    </option>
                @endforeach
            </select>
            @error('pais_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Idioma Preferido</label>
            <select name="idioma_id" class="form-select @error('idioma_id') is-invalid @enderror">
                <option value="">Seleccionar idioma...</option>
                @foreach($idiomas ?? [] as $idioma)
                    <option value="{{ $idioma->id }}"
                        {{ (isset($cliente) && $cliente->idioma_id == $idioma->id) || old('idioma_id') == $idioma->id ? 'selected' : '' }}>
                        {{ $idioma->nombre }}
                    </option>
                @endforeach
            </select>
            @error('idioma_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">Dieta Preferida</label>
            <select name="dieta_id" class="form-select @error('dieta_id') is-invalid @enderror">
                <option value="">Sin preferencia</option>
                @foreach($dietas ?? [] as $dieta)
                    <option value="{{ $dieta->id }}"
                        {{ (isset($cliente) && $cliente->dieta_id == $dieta->id) || old('dieta_id') == $dieta->id ? 'selected' : '' }}>
                        {{ $dieta->nombre }}
                    </option>
                @endforeach
            </select>
            @error('dieta_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->email : old('email') }}">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Teléfono y WhatsApp -->
        <div class="col-md-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono"
                   class="form-control @error('telefono') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->telefono : old('telefono') }}">
            @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-3">
            <label class="form-label">WhatsApp</label>
            <input type="text" name="whatsapp"
                   class="form-control @error('whatsapp') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->whatsapp : old('whatsapp') }}">
            @error('whatsapp')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Fecha de Nacimiento -->
        <div class="col-md-4">
            <label class="form-label">Fecha de Nacimiento</label>
            <input type="text" name="fecha_nacimiento"
                   data-date-format="Y-m-d"
                   class="form-control flatpickr-date @error('fecha_nacimiento') is-invalid @enderror"
                   value="{{ isset($cliente) && $cliente->fecha_nacimiento ? $cliente->fecha_nacimiento->format('Y-m-d') : old('fecha_nacimiento') }}">
            @error('fecha_nacimiento')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Género -->
        <div class="col-md-3">
            <label class="form-label">Género</label>
            <select name="genero" class="form-select @error('genero') is-invalid @enderror">
                <option value="">Seleccionar...</option>
                <option value="male" {{ (isset($cliente) && $cliente->genero == 'male') || old('genero') == 'male' ? 'selected' : '' }}>Masculino</option>
                <option value="female" {{ (isset($cliente) && $cliente->genero == 'female') || old('genero') == 'female' ? 'selected' : '' }}>Femenino</option>
                <option value="other" {{ (isset($cliente) && $cliente->genero == 'other') || old('genero') == 'other' ? 'selected' : '' }}>Otro</option>
            </select>
            @error('genero')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Pasaporte Expiración -->
        <div class="col-md-5">
            <label class="form-label">Pasaporte Expiración</label>
            <input type="text" name="pasaporte_expiracion"
                   data-date-format="Y-m-d"
                   class="form-control flatpickr-date @error('pasaporte_expiracion') is-invalid @enderror"
                   value="{{ isset($cliente) && $cliente->pasaporte_expiracion ? $cliente->pasaporte_expiracion->format('Y-m-d') : old('pasaporte_expiracion') }}">
            @error('pasaporte_expiracion')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Notas Médicas -->
        <div class="col-12">
            <label class="form-label">Notas Médicas / Requisitos Especiales</label>
            <textarea name="notas_medicas" class="form-control @error('notas_medicas') is-invalid @enderror" rows="3">{{ isset($cliente) ? $cliente->notas_medicas : old('notas_medicas') }}</textarea>
            @error('notas_medicas')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Contacto de Emergencia -->
        <div class="col-md-6">
            <label class="form-label">Contacto de Emergencia</label>
            <input type="text" name="contacto_emergencia"
                   class="form-control @error('contacto_emergencia') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->contacto_emergencia : old('contacto_emergencia') }}">
            @error('contacto_emergencia')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label">Teléfono de Emergencia</label>
            <input type="text" name="telefono_emergencia"
                   class="form-control @error('telefono_emergencia') is-invalid @enderror"
                   value="{{ isset($cliente) ? $cliente->telefono_emergencia : old('telefono_emergencia') }}">
            @error('telefono_emergencia')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Estado -->
        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <div class="form-check form-switch mt-2">
                <input type="hidden" name="activo" value="0">
                <input type="checkbox"
                       name="activo"
                       id="activo"
                       value="1"
                       class="form-check-input"
                       {{ (isset($cliente) ? $cliente->activo : old('activo', true)) ? 'checked' : '' }}>
                <label class="form-check-label" for="activo">Cliente Activo</label>
            </div>
        </div>

    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            <i class="ri-save-line"></i>
            {{ isset($cliente) ? 'Actualizar Cliente' : 'Guardar Nuevo Cliente' }}
        </button>
        <a href="{{ route('admin.clientes.index') }}" class="btn btn-secondary ms-2">Cancelar</a>
    </div>
</form>
