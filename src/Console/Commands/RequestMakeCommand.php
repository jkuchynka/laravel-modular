<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class RequestMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
