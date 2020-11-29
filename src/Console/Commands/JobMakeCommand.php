<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\JobMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class JobMakeCommand extends BaseCommand
{
    use GeneratesForModule;

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
