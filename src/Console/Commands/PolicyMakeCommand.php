<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class PolicyMakeCommand extends BaseCommand
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
        $stub = $this->replaceUserNamespace(
            parent::buildClass($name)
        );

        $model = $this->option('model');

        return $model ? $this->replaceModel($stub, $model) : $stub;
    }
}
