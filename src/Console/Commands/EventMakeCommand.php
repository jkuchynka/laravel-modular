<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\EventMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class EventMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
