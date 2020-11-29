<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\MailMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class MailMakeCommand extends BaseCommand
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
