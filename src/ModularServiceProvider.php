<?php

namespace Modular;

use Illuminate\Support\ServiceProvider;
use Modular\Providers\ConsoleServiceProvider;
use Modular\Providers\ModuleServiceProvider;
use Modular\Providers\RouteServiceProvider;

class ModularServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__.'/../config/modular.php';

        $this->mergeConfigFrom($configPath, 'modular');
        $this->publishes([
            $configPath => config_path('modular.php')
        ], 'config');
    }

    public function register()
    {
        $this->app->register(ModuleServiceProvider::class);
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }
}
