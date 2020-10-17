<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\JobMakeCommand as BaseCommand;

class JobMakeCommand extends BaseCommand
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
        return $this->getModule()->path('jobs');
    }
}
