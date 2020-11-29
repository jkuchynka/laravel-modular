<?php

namespace Modular\Tests;

use Illuminate\Foundation\Application;
use Modular\Tests\stubs\Posts\PostsModule;
use Modular\Tests\stubs\Users\UsersModule;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('modular', [
            'modules' => [
                PostsModule::class => [],
                UsersModule::class => [],
            ]
        ]);
    }
}
