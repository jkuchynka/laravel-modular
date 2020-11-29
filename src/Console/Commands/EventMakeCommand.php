<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\EventMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class EventMakeCommand extends BaseCommand
{
    use GeneratesForModule;

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
