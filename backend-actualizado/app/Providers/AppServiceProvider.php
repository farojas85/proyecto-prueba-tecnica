<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Dedoc\Scramble\Scramble;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        Scramble::ignoreDefaultRoutes();
    }

    public function boot()
    {
        // Registrar rutas de documentación personalizadas en api/docs
        Scramble::registerUiRoute('api/docs');
        Scramble::registerJsonSpecificationRoute('api/docs.json');
    }
}
