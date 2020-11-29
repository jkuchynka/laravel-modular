<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class ObserverMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $model = $this->option('model');

        return $model ? $this->replaceModel($stub, $model) : $stub;
    }
}
