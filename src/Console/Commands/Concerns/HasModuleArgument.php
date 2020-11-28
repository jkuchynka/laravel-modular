<?php

namespace Modular\Console\Commands\Concerns;

use Modular\Modular;
use Symfony\Component\Console\Input\InputArgument;

trait HasModuleArgument
{
    protected $module;

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        $arguments = parent::getArguments();
        $moduleArgument = ['module', InputArgument::REQUIRED, 'The name or key of the module'];
        array_unshift($arguments, $moduleArgument);
        return $arguments;
    }

    /**
     * Get the current module config
     *
     * @return Dot
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
}
