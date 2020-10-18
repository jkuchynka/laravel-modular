<?php

namespace Modular\Tests\stubs;

use Modular\Module;

class InvalidNoNameModule extends Module
{
    protected $key = 'invalid';

    protected function config(): array
    {
        return [];
    }
}
