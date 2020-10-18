<?php

namespace Modular\Tests;

use Illuminate\Container\Container;
use Modular\Exceptions\InvalidModuleException;
use Modular\Exceptions\ModuleNotFoundException;
use Modular\Modular;
use Modular\Tests\stubs\Posts\PostsModule;

class ModularTest extends BaseTestCase
{
    public function testBootModules()
    {
        $modular = new Modular($this->app);
        $modular->bootModules();

        $this->assertEquals('posts', $modular->getModule('posts')->key);
    }

    public function testBootModulesChecksDependencies()
    {
        $this->app['config']->set('modular.modules', [
            PostsModule::class => []
        ]);
        $modular = new Modular($this->app);

        $this->expectException(ModuleNotFoundException::class);

        $modular->bootModules();
    }
}
