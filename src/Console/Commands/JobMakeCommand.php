<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\JobMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class JobMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
