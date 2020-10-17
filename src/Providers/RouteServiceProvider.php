<?php

namespace Modular\Providers;

use \Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;
use Modular\Modular;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    protected function loadRoutes()
    {
        $modular = $this->app->make(Modular::class);
        $modules = $modular->getModules();
        foreach ($modules as $module) {
            $module->loadRoutes();
        }
    }
}
