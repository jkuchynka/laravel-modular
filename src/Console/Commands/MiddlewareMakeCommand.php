<?php

namespace Modular\Console\Commands;

use Illuminate\Routing\Console\MiddlewareMakeCommand as BaseCommand;

class MiddlewareMakeCommand extends BaseCommand
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
        return $this->getModule()->path('middleware');
    }
}
