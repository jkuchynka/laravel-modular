<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ProviderMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class ProviderMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
