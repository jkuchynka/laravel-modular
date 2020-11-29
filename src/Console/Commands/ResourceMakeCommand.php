<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ResourceMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class ResourceMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    protected $modularType = 'resources';
}
