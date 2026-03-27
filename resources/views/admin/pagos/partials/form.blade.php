{{-- Partial del formulario de pago. Requiere: $reservas (collection) y opcionalmente $pago, $reserva --}}

@php
    $editando   = isset($pago) && $pago->exists;
    $reservaFija = $reserva ?? ($pago->reserva ?? null);
@endphp

<div class="row g-3">

    {{-- ── Reserva ── --}}
    <div class="col-md-8">
        <label class="form-label fw-semibold">Reserva <span class="text-danger">*</span></label>
        @if($editando || $reservaFija)
            {{-- En edición o creación desde una reserva: mostrar fija --}}
            <input type="hidden" name="reserva_id" value="{{ $reservaFija->id }}">
            <div class="form-control bg-light d-flex align-items-center gap-2">
                <i class="ri-file-list-3-line text-primary"></i>
                <span class="fw-semibold">{{ $reservaFija->codigo_reserva }}</span>
                <span class="text-muted">—</span>
                <span>{{ $reservaFija->cliente->nombre_completo ?? '—' }}</span>
                @if($reservaFija->tour)
                    <span class="badge bg-info ms-1">{{ $reservaFija->tour->nombre_tour }}</span>
                @endif
            </div>
        @else
            <select name="reserva_id" id="selectReserva"
                    class="form-select @error('reserva_id') is-invalid @enderror"
                    onchange="cargarSaldoReserva(this.value)" required>
                <option value="">— Seleccionar reserva —</option>
                @foreach($reservas ?? [] as $r)
                    <option value="{{ $r->id }}"
                            data-saldo="{{ $r->saldo_pendiente }}"
                            data-precio="{{ $r->precio_final }}"
                            data-moneda="{{ $r->moneda }}"
                            data-pagado="{{ $r->monto_pagado }}"
                            {{ old('reserva_id') == $r->id ? 'selected' : '' }}>
                        {{ $r->codigo_reserva }} — {{ $r->cliente->nombre_completo ?? '—' }}
                        @if($r->tour) [{{ $r->tour->nombre_tour }}] @endif
                        (Saldo: {{ $r->moneda }} {{ number_format($r->saldo_pendiente, 2) }})
                    </option>
                @endforeach
            </select>
            @error('reserva_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        @endif
    </div>

    {{-- ── Fecha de Pago ── --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Fecha de Pago <span class="text-danger">*</span></label>
        <input type="date" name="fecha_pago"
               class="form-control @error('fecha_pago') is-invalid @enderror"
               value="{{ $editando ? $pago->fecha_pago->format('Y-m-d') : old('fecha_pago', date('Y-m-d')) }}" required>
        @error('fecha_pago')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ── Resumen de saldo ── --}}
    <div class="col-12" id="saldoResumenBox" style="{{ ($editando || $reservaFija) ? '' : 'display:none' }}">
        @php
            $rsv = $reservaFija ?? ($editando ? $pago->reserva : null);
        @endphp
        @if($rsv)
            @include('admin.pagos.partials.saldo-resumen', ['rsv' => $rsv])
        @else
            <div id="saldoResumenContent"></div>
        @endif
    </div>

    {{-- ── Monto ── --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">Monto <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="monto" id="inputMonto"
               class="form-control @error('monto') is-invalid @enderror"
               value="{{ $editando ? $pago->monto : old('monto') }}"
               placeholder="0.00" required>
        @error('monto')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text" id="montoHint"></div>
    </div>

    <input type="hidden" name="moneda" value="USD">

    {{-- ── Tipo de Pago ── --}}
    <div class="col-md-3">
        <label class="form-label fw-semibold">Tipo de Pago <span class="text-danger">*</span></label>
        <select name="tipo_pago" class="form-select @error('tipo_pago') is-invalid @enderror" required>
            @foreach(\App\Models\Pago::tiposLabel() as $val => $label)
                <option value="{{ $val }}"
                    {{ ($editando && $pago->tipo_pago === $val) || old('tipo_pago', 'parcial') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('tipo_pago')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ── Estado ── --}}
    <div class="col-md-2">
        <label class="form-label fw-semibold">Estado</label>
        <select name="estado" class="form-select @error('estado') is-invalid @enderror">
            <option value="confirmado" {{ ($editando && $pago->estado === 'confirmado') || old('estado', 'confirmado') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
            <option value="pendiente"  {{ ($editando && $pago->estado === 'pendiente')  || old('estado') === 'pendiente'  ? 'selected' : '' }}>Pendiente</option>
            <option value="rechazado"  {{ ($editando && $pago->estado === 'rechazado')  || old('estado') === 'rechazado'  ? 'selected' : '' }}>Rechazado</option>
            <option value="devuelto"   {{ ($editando && $pago->estado === 'devuelto')   || old('estado') === 'devuelto'   ? 'selected' : '' }}>Devuelto</option>
        </select>
    </div>

    {{-- ── Método de Pago ── --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Método de Pago <span class="text-danger">*</span></label>
        <select name="metodo_pago" id="selectMetodo"
                class="form-select @error('metodo_pago') is-invalid @enderror"
                onchange="toggleBancoFields(this.value)" required>
            @foreach(\App\Models\Pago::metodosLabel() as $val => $label)
                <option value="{{ $val }}"
                    {{ ($editando && $pago->metodo_pago === $val) || old('metodo_pago') === $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('metodo_pago')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ── Número de Operación ── --}}
    <div class="col-md-4">
        <label class="form-label fw-semibold">Código / N° Operación</label>
        <input type="text" name="numero_operacion"
               class="form-control @error('numero_operacion') is-invalid @enderror"
               value="{{ $editando ? $pago->numero_operacion : old('numero_operacion') }}"
               placeholder="Código Yape, N° transferencia, etc.">
        @error('numero_operacion')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- ── Banco Origen / Destino (solo para transferencia) ── --}}
    <div class="col-md-4" id="bancoOrigenBox" style="display:none">
        <label class="form-label">Banco Origen</label>
        <input type="text" name="banco_origen"
               class="form-control"
               value="{{ $editando ? $pago->banco_origen : old('banco_origen') }}"
               placeholder="BCP, BBVA, Interbank...">
    </div>

    <div class="col-md-4" id="bancoDestinoBox" style="display:none">
        <label class="form-label">Banco Destino / Cuenta</label>
        <input type="text" name="banco_destino"
               class="form-control"
               value="{{ $editando ? $pago->banco_destino : old('banco_destino') }}"
               placeholder="Cuenta destino">
    </div>

    {{-- ── Notas ── --}}
    <div class="col-12">
        <label class="form-label">Notas</label>
        <textarea name="notas" class="form-control" rows="2"
                  placeholder="Observaciones adicionales del pago...">{{ $editando ? $pago->notas : old('notas') }}</textarea>
    </div>

</div>

<script>
function toggleBancoFields(metodo) {
    const show = metodo === 'transferencia_bancaria';
    document.getElementById('bancoOrigenBox').style.display  = show ? '' : 'none';
    document.getElementById('bancoDestinoBox').style.display = show ? '' : 'none';
}

function cargarSaldoReserva(reservaId) {
    const box = document.getElementById('saldoResumenBox');
    const content = document.getElementById('saldoResumenContent');
    if (!reservaId) { box.style.display = 'none'; return; }

    const select = document.getElementById('selectReserva');
    const opt = select ? select.querySelector(`option[value="${reservaId}"]`) : null;
    if (opt) {
        const saldo   = parseFloat(opt.dataset.saldo  ?? 0);
        const precio  = parseFloat(opt.dataset.precio ?? 0);
        const pagado  = parseFloat(opt.dataset.pagado ?? 0);
        const moneda  = opt.dataset.moneda ?? 'USD';
        const pct     = precio > 0 ? Math.min(100, Math.round((pagado / precio) * 100)) : 0;

        if (content) {
            content.innerHTML = `
                <div class="alert alert-info py-2 mb-0">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <div><span class="text-muted small">Precio Final:</span>
                            <strong class="ms-1">${moneda} ${precio.toFixed(2)}</strong></div>
                        <div><span class="text-muted small">Pagado:</span>
                            <strong class="ms-1 text-success">${moneda} ${pagado.toFixed(2)}</strong></div>
                        <div><span class="text-muted small">Saldo Pendiente:</span>
                            <strong class="ms-1 text-danger">${moneda} ${saldo.toFixed(2)}</strong></div>
                        <div class="ms-auto" style="min-width:120px">
                            <div class="progress" style="height:8px">
                                <div class="progress-bar ${pct >= 100 ? 'bg-success' : pct >= 50 ? 'bg-info' : 'bg-warning'}"
                                     style="width:${pct}%"></div>
                            </div>
                            <small class="text-muted">${pct}% pagado</small>
                        </div>
                    </div>
                </div>`;
        }
        box.style.display = '';

        // Sugerir monto = saldo pendiente si el campo está vacío
        const montoInput = document.getElementById('inputMonto');
        if (montoInput && !montoInput.value && saldo > 0) {
            montoInput.value = saldo.toFixed(2);
            document.getElementById('montoHint').textContent = `Saldo pendiente: ${moneda} ${saldo.toFixed(2)}`;
        }
    }
}

// Inicializar estado del método de pago
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('selectMetodo');
    if (sel) toggleBancoFields(sel.value);

    const selRes = document.getElementById('selectReserva');
    if (selRes && selRes.value) cargarSaldoReserva(selRes.value);
});
</script>
