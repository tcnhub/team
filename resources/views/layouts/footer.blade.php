<footer class="footer">
    @php
        $empresa = $globalConfiguraciones['Nombre de la Empresa'] ?? config('app.name');
        $ruc = $globalConfiguraciones['RUC'] ?? null;
        $direccion = $globalConfiguraciones['Direccion'] ?? null;
        $telefono = $globalConfiguraciones['telefono'] ?? null;
        $web = $globalConfiguraciones['Pagina Web'] ?? null;

        $detalleEmpresa = collect([
            $empresa,
            $ruc ? 'RUC: ' . $ruc : null,
            $direccion,
            $telefono ? 'Tel: ' . $telefono : null,
        ])->filter()->implode(' | ');
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <script>document.write(new Date().getFullYear())</script> © {{ $detalleEmpresa }}
                @if ($web)
                    | <a href="{{ $web }}" target="_blank" rel="noopener noreferrer">{{ $web }}</a>
                @endif
            </div>
            <div class="col-sm-6">
                <div class="text-sm-end d-none d-sm-block">
                    Desarrollado por <a href="https://www.tecnodus.com" target="_blank" rel="noopener noreferrer">Tecnodus</a>
                </div>
            </div>
        </div>
    </div>
</footer>
