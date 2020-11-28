<?php

namespace Modular\Console\Commands\Concerns;

use Modular\Modular;
use Modular\Module;
use Symfony\Component\Console\Input\InputArgument;

trait HasModuleArgument
{
    /**
     * @var Module
     */
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
     * Get the module from the argument
     *
     * @return Module
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
