@php $pct = $rsv->precio_final > 0 ? min(100, round(($rsv->monto_pagado / $rsv->precio_final) * 100)) : 0; @endphp
<div class="alert alert-info py-2 mb-0">
    <div class="d-flex flex-wrap gap-3 align-items-center">
        <div><span class="text-muted small">Precio Final:</span>
            <strong class="ms-1">{{ $rsv->moneda }} {{ number_format($rsv->precio_final, 2) }}</strong></div>
        <div><span class="text-muted small">Pagado:</span>
            <strong class="ms-1 text-success">{{ $rsv->moneda }} {{ number_format($rsv->monto_pagado, 2) }}</strong></div>
        <div><span class="text-muted small">Saldo Pendiente:</span>
            <strong class="ms-1 text-danger">{{ $rsv->moneda }} {{ number_format($rsv->saldo_pendiente, 2) }}</strong></div>
        <div class="ms-auto" style="min-width:120px">
            <div class="progress" style="height:8px">
                <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : ($pct >= 50 ? 'bg-info' : 'bg-warning') }}"
                     style="width:{{ $pct }}%"></div>
            </div>
            <small class="text-muted">{{ $pct }}% pagado</small>
        </div>
    </div>
</div>
