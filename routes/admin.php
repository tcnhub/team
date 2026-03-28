<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\AgenteController;
use App\Http\Controllers\Admin\CategoriaController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DietaController;
use App\Http\Controllers\Admin\IdiomaController;
use App\Http\Controllers\Admin\PagoController;
use App\Http\Controllers\Admin\PaisController;
use App\Http\Controllers\Admin\PasajeroController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\Admin\TourAvailabilityController;
use App\Http\Controllers\Admin\TourCalendarController;
use App\Http\Controllers\Admin\TourController;

use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
            Route::post('/login', [AdminLoginController::class, 'store'])->name('login.store');
        });

        Route::middleware('auth:admin')->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

            // ── Tours ──────────────────────────────────────────────────────
            Route::resource('tours', TourController::class);

            // Calendario de disponibilidad por tour
            Route::prefix('tours/{tour}/calendar')->name('tours.calendar.')->group(function () {
                Route::get('create', [TourCalendarController::class, 'create'])->name('create');
                Route::post('/', [TourCalendarController::class, 'store'])->name('store');
                Route::get('{calendar}', [TourCalendarController::class, 'show'])->name('show');
                Route::delete('{calendar}', [TourCalendarController::class, 'destroy'])->name('destroy');
                Route::post('{calendar}/bulk', [TourCalendarController::class, 'bulkUpdate'])->name('bulk');
                Route::patch('{calendar}/days/{availability}', [TourCalendarController::class, 'updateDay'])->name('days.update');
            });

            // Calendario de reservas por tour + AJAX store
            Route::get(
                'tours/{tour}/reservas-calendario',
                [TourCalendarController::class, 'reservasCalendario']
            )->name('tours.reservas.calendario');

            Route::post(
                'tours/{tour}/reservas-ajax',
                [ReservaController::class, 'storeAjax']
            )->name('tours.reservas.store-ajax');

            // AJAX: actualización rápida de un día (desde la grilla de calendario)
            Route::patch(
                'tours/{tour}/availability/{availability}',
                [TourAvailabilityController::class, 'update']
            )->name('tours.availability.update');

            // ── Reservas ───────────────────────────────────────────────────
            Route::resource('reservas', ReservaController::class);
            Route::get('tours/{tour}/availabilities-json', [ReservaController::class, 'availabilitiesPorTour'])
                ->name('tours.availabilities.json');

            // ── Clientes ───────────────────────────────────────────────────
            Route::resource('clientes', ClienteController::class);
            Route::resource('pasajeros', PasajeroController::class);
            Route::get('pasajeros/reservas/{reserva}/relacion', [PasajeroController::class, 'reservaRelacion'])
                ->name('pasajeros.reserva-relacion');
            Route::get('clientes/buscar-documento', [ClienteController::class, 'buscarPorDocumento'])
                ->name('clientes.buscar-documento');
            Route::post('clientes/store-quick', [ClienteController::class, 'storeQuick'])
                ->name('clientes.store-quick');

            // ── Catálogos relacionados con clientes ────────────────────────
            Route::resource('dietas', DietaController::class);
            Route::get('dietas/buscar', [DietaController::class, 'buscar'])->name('dietas.buscar');

            Route::resource('idiomas', IdiomaController::class);
            Route::get('idiomas/buscar', [IdiomaController::class, 'buscar'])->name('idiomas.buscar');

            Route::resource('paises', PaisController::class);
            Route::get('paises/buscar', [PaisController::class, 'buscar'])->name('paises.buscar');

            // ── Agentes ────────────────────────────────────────────────────
            Route::resource('agentes', AgenteController::class);

            // ── Pagos ──────────────────────────────────────────────────────
            Route::resource('pagos', PagoController::class);

            // ── Categorías de Tours ────────────────────────────────────────
            Route::resource('categorias', CategoriaController::class);
        });
    });
