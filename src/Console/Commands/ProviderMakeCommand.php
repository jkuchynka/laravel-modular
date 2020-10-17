<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand as BaseCommand;

class ProviderMakeCommand extends BaseCommand
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
        return $this->getModule()->path('providers');
    }
}
