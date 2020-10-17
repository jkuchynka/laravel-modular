<?php

namespace Modular\Console\Commands;

use Illuminate\Database\Console\Seeds\SeederMakeCommand as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeederMakeCommand extends BaseCommand
{
    use Concerns\HasModuleArgument,
        Concerns\GeneratesForModule;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/seeder.stub';
    }

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('seeds');
    }
}
