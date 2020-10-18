<?php

namespace Modular\Tests\stubs;

use Modular\Module;

class InvalidNoKeyModule extends Module
{
    protected $name = 'Invalid';

    protected function config(): array
    {
        return [];
    }
}
