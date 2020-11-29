<?php

namespace Modular\Console\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand as BaseCommand;
use Illuminate\Support\Str;
use Modular\Console\Commands\Concerns\GeneratesForModule;
use Modular\Support\Namespaces;

class SeederMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return $this->getModule()->getNamespace('seeds');
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        return $this->getModule()->getPath('seeds').'/'.$name.'.php';
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/console.stub';
    }
}
