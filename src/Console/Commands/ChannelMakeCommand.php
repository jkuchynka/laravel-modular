<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ChannelMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class ChannelMakeCommand extends BaseCommand
{
    use GeneratesForModule;
}
