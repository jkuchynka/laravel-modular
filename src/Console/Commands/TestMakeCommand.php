<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\TestMakeCommand as BaseCommand;
use Illuminate\Support\Facades\File;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class TestMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:test {module : The name or key of the module} {name : The name of the class} {--unit : Create a unit test}';

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        $path = $this->getModule()->path('tests');
        $path .= $this->option('unit') ? '/Unit' : '/Feature';
        return $path;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('unit')
                    ? __DIR__.'/stubs/test.unit.stub'
                    : __DIR__.'/stubs/test.stub';
    }
}
