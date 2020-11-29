<?php

namespace Modular\Tests;

use Illuminate\Container\Container;
use Modular\Exceptions\InvalidModuleException;
use Modular\Exceptions\ModuleNotFoundException;
use Modular\Modular;
use Modular\Module;
use Modular\Tests\stubs\Posts\PostsModule;

class ModularTest extends BaseTestCase
{
    protected $modular;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modular = new Modular($this->app);
        $this->modular->bootModules();
    }

    public function testBootModules()
    {
        $this->assertEquals('posts', $this->modular->getModule('posts')->key);
    }

    public function testBootModulesChecksDependencies()
    {
        $this->app['config']->set('modular.modules', [
            PostsModule::class => [
                'dependsOn' => ['foo_bar'],
            ],
        ]);
        $modular = new Modular($this->app);

        $this->expectException(ModuleNotFoundException::class);

        $modular->bootModules();
    }

    public function testGetModuleByKey()
    {
        $this->assertInstanceOf(Module::class, $this->modular->getModule('posts'));
    }

    public function testGetModuleByName()
    {
        $this->assertInstanceOf(Module::class, $this->modular->getModule('Posts'));
    }

    public function testGetModuleNotExistsThrowsException()
    {
        $this->expectException(ModuleNotFoundException::class);

        $this->modular->getModule('module_not_found');
    }

    public function testGetModules()
    {
        $this->assertIsArray($this->modular->getModules());
        $this->assertInstanceOf(PostsModule::class, $this->modular->getModules()['posts']);
    }
}
