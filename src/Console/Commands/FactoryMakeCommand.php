<?php

namespace Modular\Console\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class FactoryMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('factories');
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/factory.stub';
    }
}
