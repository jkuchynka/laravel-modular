<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\TestMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class TestMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'make:test {module : The name or key of the module} {name : The name of the class} {--unit : Create a unit test} {--debug : Debug this command}';

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        $ns = $this->getModule()->getNamespace('tests');

        if ($this->option('unit')) {
            return $ns.'\Unit';
        } else {
            return $ns.'\Feature';
        }
    }
}
