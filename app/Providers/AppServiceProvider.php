<?php

namespace App\Providers;

use App\Models\Configuracion;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('configuraciones')) {
            return;
        }

        $configuraciones = Configuracion::query()
            ->orderBy('nombre')
            ->pluck('valor', 'nombre')
            ->toArray();

        view()->share('globalConfiguraciones', $configuraciones);
        app()->instance('globalConfiguraciones', $configuraciones);
    }
}
