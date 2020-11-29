<?php

namespace Modular\Console\Commands;

use Illuminate\Routing\Console\MiddlewareMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class MiddlewareMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('middleware');
    }
}
