<?php

namespace Modular\Console\Commands\Concerns;

use Base\Helpers\Common;
use Illuminate\Support\Str;
use Modular\Support\Namespaces;

trait GeneratesForModule
{
    /**
     * Get the path for the built class
     *
     * @return string
     */
    abstract protected function getTargetPath();

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
        return $this->getModule()->getNamespace('models');
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
}
