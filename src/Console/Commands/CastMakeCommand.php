<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\CastMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class CastMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
