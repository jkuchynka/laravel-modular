<?php

namespace Modular\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResourceTestsMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:resource-tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create resource tests for a model.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Test';

    /**
     * The module config
     *
     * @var Dot
     */
    protected $module;

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::REQUIRED, 'The module key or name'],
            ['model', InputArgument::REQUIRED, 'The name of the model']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['feature', null, InputOption::VALUE_OPTIONAL, 'Create a feature test'],
            ['unit', null, InputOption::VALUE_OPTIONAL, 'Create a unit test'],
            ['force', null, InputOption::VALUE_NONE, 'Force overwrite files']
        ];
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $modules = resolve('modules');
        $this->module = $modules->getModule($this->getModuleInput());

        $unit = $feature = false;
        if ($this->option('unit')) {
            $unit = true;
        }
        if ($this->option('feature')) {
            $feature = true;
        }
        if (! $this->option('unit') && ! $this->option('feature')) {
            $unit = $feature = true;
        }

        foreach (['unit', 'feature'] as $type) {
            if (! $$type) {
                return;
            }
            switch ($type) {
                case 'unit':
                    $testClassSuffix = 'Test';
                    $testSubDir = 'Unit';
                    $stub = $this->getStub(true);
                break;
                case 'feature':
                    $testClassSuffix = 'ApiTest';
                    $testSubDir = 'Feature';
                    $stub = $this->getStub();
                break;
            }

            $model = $this->getModelInput();
            $class = $model . 's' . $testClassSuffix;
            $name = $class . '.php';
            $path = $this->module['paths.module'] . '/Tests/' . $testSubDir . '/' . $name;

            if ((! $this->hasOption('force') ||
                 ! $this->option('force')) &&
                 $this->alreadyExists($path)) {
                $this->error($class . ' already exists!');

                return false;
            }

            $this->makeDirectory($path);

            $namespace = $this->module['namespace'];
            $controllerNamespace = $namespace;
            $controllerNamespace .= $this->module['paths.controllers'] ? '\\' . str_replace('/', '\\', $this->module['paths.controllers']) : '';
            $controller = $model . 'sController';
            $testNamespace = $namespace . '\\Tests\\' . $testSubDir;

            $stub = $this->files->get($stub);

            $stub = str_replace(
                [
                    '{{ namespace }}',
                    '{{ controllerNamespace }}',
                    '{{ testNamespace }}',
                    '{{ class }}',
                    '{{ controller }}',
                    '{{ model }}',
                    '{{ module }}'
                ],
                [
                    $namespace,
                    $controllerNamespace,
                    $testNamespace,
                    $class,
                    $controller,
                    $model,
                    $this->module['key']
                ],
                $stub
            );

            $this->files->put($path, $stub);

            $this->info($class . ' created successfully.');
        }
    }

    /**
     * Determine if the file already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($path)
    {
        return $this->files->exists($path);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub($unit = false)
    {
        return $unit
                    ? $this->resolveStubPath('/stubs/resource.test.unit.stub')
                    : $this->resolveStubPath('/stubs/resource.test.feature.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    /**
     * Get the desired class name.
     *
     * @return string
     */
    protected function getModelInput()
    {
        return trim($this->argument('model'));
    }

    /**
     * Get the desired module key.
     *
     * @return string
     */
    protected function getModuleInput()
    {
        return trim($this->argument('module'));
    }
}
