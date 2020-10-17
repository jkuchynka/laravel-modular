<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\EventMakeCommand as BaseCommand;

class EventMakeCommand extends BaseCommand
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
        return $this->getModule()->path('events');
    }
}
