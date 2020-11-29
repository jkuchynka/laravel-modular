<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\RuleMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class RuleMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
