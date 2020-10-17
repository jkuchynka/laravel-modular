<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand as BaseCommand;

class ObserverMakeCommand extends BaseCommand
{
    use Concerns\HasModuleArgument,
        Concerns\GeneratesForModule;

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('observers');
    }
}
