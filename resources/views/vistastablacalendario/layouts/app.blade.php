<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tours') — Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #212529; }
        .sidebar .nav-link { color: rgba(255,255,255,.75); border-radius: .375rem; }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); }
        .sidebar .nav-link i { width: 20px; }
        .badge-disponible { font-size: .7rem; }
        .table td, .table th { vertical-align: middle; }
    </style>
    @yield('head')
</head>
<body>
<div class="container-fluid">
    <div class="row">

        {{-- Sidebar --}}
        <nav class="col-md-2 sidebar py-3 px-2">
            <a class="navbar-brand text-white fw-bold fs-5 d-block mb-4 px-2" href="{{ route('tours.index') }}">
                <i class="bi bi-map me-2"></i>Tours
            </a>
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('tours.*') ? 'active' : '' }}"
                       href="{{ route('tours.index') }}">
                        <i class="bi bi-compass me-2"></i>Tours
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}"
                       href="{{ route('reservations.index') }}">
                        <i class="bi bi-calendar-check me-2"></i>Reservaciones
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="{{ route('reservations.create') }}">
                        <i class="bi bi-plus-circle me-2"></i>Nueva reservación
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Contenido --}}
        <main class="col-md-10 py-4 px-4">
            {{-- Alertas --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
