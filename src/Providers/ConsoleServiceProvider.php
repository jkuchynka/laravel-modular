<?php

namespace Modular\Providers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Modular\Console\Commands\CastMakeCommand;
use Modular\Console\Commands\ChannelMakeCommand;
use Modular\Console\Commands\ConsoleMakeCommand;
use Modular\Console\Commands\ControllerMakeCommand;
use Modular\Console\Commands\EventMakeCommand;
use Modular\Console\Commands\ExceptionMakeCommand;
use Modular\Console\Commands\FactoryMakeCommand;
use Modular\Console\Commands\JobMakeCommand;
use Modular\Console\Commands\ListenerMakeCommand;
use Modular\Console\Commands\MailMakeCommand;
use Modular\Console\Commands\MiddlewareMakeCommand;
use Modular\Console\Commands\MigrateMakeCommand;
use Modular\Console\Commands\ModelMakeCommand;
use Modular\Console\Commands\ModuleConfigsCommand;
use Modular\Console\Commands\ModuleMakeCommand;
use Modular\Console\Commands\NotificationMakeCommand;
use Modular\Console\Commands\ObserverMakeCommand;
use Modular\Console\Commands\PolicyMakeCommand;
use Modular\Console\Commands\ProviderMakeCommand;
use Modular\Console\Commands\RequestMakeCommand;
use Modular\Console\Commands\ResourceMakeCommand;
use Modular\Console\Commands\RuleMakeCommand;
use Modular\Console\Commands\SeedCommand;
use Modular\Console\Commands\SeederMakeCommand;
use Modular\Console\Commands\TestMakeCommand;
use Modular\Modular;
use Symfony\Component\Finder\Finder;

class ConsoleServiceProvider extends ServiceProvider
{
    protected $laravelCommands = [
        CastMakeCommand::class,
        ChannelMakeCommand::class,
        ConsoleMakeCommand::class,
        ControllerMakeCommand::class,
        EventMakeCommand::class,
        ExceptionMakeCommand::class,
        FactoryMakeCommand::class,
        JobMakeCommand::class,
        ListenerMakeCommand::class,
        MailMakeCommand::class,
        MiddlewareMakeCommand::class,
        MigrateMakeCommand::class,
        ModelMakeCommand::class,
        ModuleConfigsCommand::class,
        ModuleMakeCommand::class,
        NotificationMakeCommand::class,
        ObserverMakeCommand::class,
        PolicyMakeCommand::class,
        ProviderMakeCommand::class,
        RequestMakeCommand::class,
        ResourceMakeCommand::class,
        RuleMakeCommand::class,
        SeedCommand::class,
        SeederMakeCommand::class,
        TestMakeCommand::class
    ];

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->laravelCommands);
        }
    }

    public function register()
    {
        $this->app->singleton('command.seed', function ($app) {
            return new SeedCommand($app['db']);
        });

        // Fix unresolvable dependency error when resolving $customStubPath
        // in MigrationCreator
        $this->app->when(MigrationCreator::class)
            ->needs('$customStubPath')
            ->give(function ($app) {
                return $app->basePath('stubs');
            });

        $this->loadModuleCommands();
    }

    protected function loadModuleCommands()
    {
        // Load commands from enabled modules
        foreach ($this->app->make(Modular::class)->getModules() as $module) {
            $path = $module->get('paths.module') . '/' . $module->get('paths.commands');
            if (is_dir($path)) {
                // @todo: Make sure this works with any module namespace and path
                // $namespace = $module['namespace'];
                $namespace = '';
                foreach ((new Finder)->in($path)->files() as $command) {
                    $command = $namespace.str_replace(
                            ['/', '.php'],
                            ['\\', ''],
                            Str::after($command->getPathname(), realpath(app_path()).DIRECTORY_SEPARATOR)
                        );

                    if (is_subclass_of($command, Command::class) &&
                        ! (new ReflectionClass($command))->isAbstract()) {
                        Artisan::starting(function ($artisan) use ($command) {
                            $artisan->resolve($command);
                        });
                    }
                }
            }
        }
    }
}
