<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class PolicyMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
