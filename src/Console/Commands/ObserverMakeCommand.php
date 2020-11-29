<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ObserverMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class ObserverMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
