<?php

namespace Modular\Console\Commands\Concerns;

use Base\Helpers\Common;
use Illuminate\Support\Str;
use Modular\Module;
use Modular\Support\Namespaces;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

trait GeneratesForModule
{
    /**
     * @var Module
     */
    protected $module;

    /**
     * Get the module from the argument
     *
     * @return Module
     */
    protected function getModule()
    {
        if (! $this->module) {
            $modular = app('modular');
            $module = trim($this->argument('module'));
            $this->module = $modular->getModule($module);
        }
        return $this->module;
    }

    public function handle()
    {
        if ($this->option('debug')) {
            $this->debug();
        }

        parent::handle();
    }

    protected function getType()
    {
        return Str::plural(Str::lower($this->type));
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return $this->getModule()->getNamespace();
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $type = isset($this->modularType) ? $this->modularType : $this->type;
        $type = Str::plural(Str::lower($type));

        return $this->getModule()->getNamespace($type);
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = str_replace('.php', '', $name);

        return parent::qualifyClass($name);
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $stub = $this
            ->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);

        return $this->replaceExtra($stub, $name);
    }

    /**
     * Replace any extra vars in the stub.
     *
     * @param $stub
     * @param $name
     * @return $this
     */
    protected function replaceExtra($stub, $name)
    {
        return $stub;
    }

    protected function getBaseClass($key, $default)
    {
        return config('modular.base.'.$key, $default);
    }

    /**
     * Get the replacement variables for the stub
     *
     * @param array $replacements
     * @return array
     */
    protected function getReplacements($replacements)
    {
        return $replacements;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace('.php', '', $name);

        $relativeNamespacedClass = ltrim(Str::replaceFirst($this->rootNamespace(), '', $name), '\\');

        return $this->getModule()->getPath().'/'.Namespaces::toPath($relativeNamespacedClass).'.php';
    }

    /**
     * Get the first view directory path from the application configuration.
     *
     * @param  string  $path
     * @return string
     */
    protected function viewPath($path = '')
    {
        return $this->getModule()->getPath('views').'/'.$path;
    }

    protected function debug()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        $stub = $this->buildClass($name);

        dd($name, $path, $stub);
    }

    /**
     * Specify the arguments and options on the command.
     *
     * @return void
     */
    protected function specifyParameters()
    {
        $arguments = $this->getArguments();
        array_unshift(
            $arguments,
            ['module', InputArgument::REQUIRED, 'The name or key of the module']
        );
        foreach ($arguments as $argument) {
            if ($argument instanceof InputArgument) {
                $this->getDefinition()->addArgument($argument);
            } else {
                call_user_func_array([$this, 'addArgument'], $argument);
            }
        }

        $options = $this->getOptions();
        $options[] = ['debug', 'd', InputOption::VALUE_NONE, 'Debug this command'];
        foreach ($options as $option) {
            if ($option instanceof InputOption) {
                $this->getDefinition()->addOption($option);
            } else {
                call_user_func_array([$this, 'addOption'], $option);
            }
        }
    }
}
