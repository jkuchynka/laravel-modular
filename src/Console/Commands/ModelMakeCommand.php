<?php

namespace Modular\Console\Commands;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Console\ModelMakeCommand as BaseCommand;
use Illuminate\Support\Str;
use Modular\Console\Commands\Concerns\GeneratesForModule;
use Modular\Support\Namespaces;
use Symfony\Component\Console\Input\InputOption;

class ModelMakeCommand extends BaseCommand
{
    use Concerns\HasModuleArgument;
    use GeneratesForModule;

    /**
     * Replace any extra vars in the stub.
     *
     * @param $stub
     * @param $name
     * @return string
     */
    protected function replaceExtra($stub, $name)
    {
        $baseClass = $this->getBaseClass('model', Model::class);

        $searches = [
            '{{ BaseModel }}' => $baseClass,
            '{{ BaseModelClass }}' => Namespaces::className($baseClass),
        ];

        foreach ($searches as $key => $val) {
            $stub = str_replace($key, $val, $stub);
        }

        return $stub;
    }

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->getPath('models');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->getModule()->getNamespace('models');
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createFactory()
    {
        $factory = Str::studly(class_basename($this->argument('name')));

        $this->call('make:factory', [
            'module' => $this->getModule()['key'],
            'name' => "{$factory}Factory",
            '--model' => $this->argument('name'),
        ]);
    }

    /**
     * Create a migration file for the model.
     *
     * @return void
     */
    protected function createMigration()
    {
        $table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

        if ($this->option('pivot')) {
            $table = Str::singular($table);
        }

        $this->call('make:migration', [
            'module' => $this->getModule()['key'],
            'name' => "create_{$table}_table",
            '--create' => $table,
        ]);
    }

    /**
     * Create a seeder file for the model.
     *
     * @return void
     */
    protected function createSeeder()
    {
        $seeder = Str::studly(class_basename($this->argument('name')));

        $this->call('make:seed', [
            'module' => $this->getModule()['key'],
            'name' => "{$seeder}Seeder",
        ]);
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $controller = Str::studly(class_basename($this->argument('name')));

        $this->call('make:controller', array_filter([
            'module' => $this->getModule()['key'],
            'name'  => "{$controller}Controller",
            '--model' => $this->option('resource') || $this->option('api') ? $this->argument('name') : null,
            '--api' => $this->option('api'),
            '--querybuilder' => $this->option('querybuilder')
        ]));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('pivot')
                    ? __DIR__.('/stubs/model.pivot.stub')
                    : __DIR__.('/stubs/model.stub');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['querybuilder', 'b', InputOption::VALUE_NONE, 'Generate an api controller with query builder methods'];
        return $options;
    }
}
