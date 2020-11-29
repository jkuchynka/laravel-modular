<?php

namespace Modular\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class QueryMakeCommand extends GeneratorCommand
{
    use GeneratesForModule;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/query.stub';
    }

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('queries');
    }

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:query';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new query builder class';
}
