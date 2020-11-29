<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\NotificationMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class NotificationMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        return parent::buildClass($name);
    }
}
