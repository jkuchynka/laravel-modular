<?php

namespace Modular\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Factory as EloquentFactory;
use Illuminate\Support\Str;
use Modular\Modular;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;
// use Base\Database\SeedCommand;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Modular::class, function ($app) {
            $modular = new Modular($app);
            $modular->bootModules();
            return $modular;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
//        die('ModuleServiceProvider:boot');
        // Get module subscribers
        /*
        $subscribers = [];
        $modulesService = $this->app['modules'];
        $modules = $modulesService->getModules();
        foreach ($modules as $module) {
            foreach ($module->get('subscribers', []) as $subscriber) {
                $subscriber = $module->classFullyNamespaced($subscriber, 'listeners');
                Event::subscribe($subscriber);
            }
        }
        */
        $this->registerFactories();
        $this->registerMigrations();
        $this->registerViews();
    }

    protected function registerFactories()
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            // Handle App\Models\User, App\User and App\Users\Models\User
            $modelName = Str::startsWith($modelName, 'App\\Models\\')
                ? Str::after($modelName, 'App\\Models\\')
                : Str::after($modelName, 'App\\');

            if (Str::contains($modelName, '\\')) {
                // This model belongs to a module
                $moduleName = Str::before($modelName, '\\');
                $modular = $this->app->make(Modular::class);
                $module = $modular->getModule($moduleName);
                $path = $module->getPath('factories');
                $nsPath = Str::replaceArray('/', '\\', $path);
                return $nsPath.'\\'.$modelName.'Factory';
            }

            return static::$namespace.$modelName.'Factory';
        });
    }

    protected function registerMigrations()
    {
        $migrator = $this->app->make('migrator');
        $modular = $this->app->make(Modular::class);
        foreach ($modular->getModules() as $module) {
            $migrator->path($module->getPath('migrations'));
        }
    }

    protected function registerViews()
    {
        $viewFactory = $this->app->make(ViewFactory::class);
        $modular = $this->app->make(Modular::class);
        foreach ($modular->getModules() as $module) {
            // Register module view paths. Instead of actual namespace (App\Auth),
            // this should be the name of the module, or trailing part of
            // namespace (Auth)
            $viewFactory->addNamespace($module->name, $module->getPath('views'));
        }
    }
}
