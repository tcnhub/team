<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\ClienteController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\Admin\TourController;   // ← Añade esta línea

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

            // Resource para Tours
            Route::resource('tours', TourController::class);
            Route::resource('reservas', ReservaController::class);
            Route::resource('clientes', ClienteController::class);
            Route::resource('dietas', ClienteController::class);
            Route::resource('idiomas', ClienteController::class);
            Route::resource('paises', ClienteController::class);

            // para busqueda de clienates por documento
            Route::get('clientes/buscar-documento', [ClienteController::class, 'buscarPorDocumento'])->name('clientes.buscar-documento');
            Route::get('paises/buscar', [PaisController::class, 'buscar'])->name('paises.buscar');
            Route::get('idiomas/buscar', [IdiomaController::class, 'buscar'])->name('idiomas.buscar');
        });
    });
