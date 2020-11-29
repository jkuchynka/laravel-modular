<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ExceptionMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class ExceptionMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
