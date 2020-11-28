<?php

namespace Modular\Providers;

use \Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseRouteServiceProvider;
use Modular\Concerns\LoadsRoutes;

class RouteServiceProvider extends BaseRouteServiceProvider
{
    /**
     * The controller namespace for the application.
     *
     * @var string|null
     */
    protected $namespace;

    protected static $loadRoutes = true;

    /**
     * Set whether to load routes from modules
     *
     * @param $loadRoutes
     */
    public static function setLoadRoutes($loadRoutes)
    {
        static::$loadRoutes = $loadRoutes;
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        if (! static::$loadRoutes) {
            return;
        }

        $modular = $this->app->make('modular');
        $modules = $modular->getModules();
        $this->routes(function () use ($modules) {
            foreach ($modules as $module) {
                if (in_array(
                    LoadsRoutes::class,
                    class_uses($module)
                )) {
                    $module->loadRoutes();
                }
//                Route::middleware('web')
//                    ->group($module->get('paths.module').'/routes.web.php');
            }
        });
    }
}
