<?php

namespace Modular\Tests\stubs\Users;

use Modular\Module;

class UsersModule extends Module
{
    protected $key = 'users';

    protected $name = 'Posts';

    protected function config() : array
    {
        return [];
    }
}
