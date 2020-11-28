<?php

namespace Modular\Tests;

use Modular\Exceptions\InvalidModuleException;
use Modular\Module;
use Modular\ModuleConfig;
use Modular\Tests\stubs\Posts\PostsModule;

class ModuleTest extends BaseTestCase
{
    public function testRequiresKey()
    {
        $this->expectException(InvalidModuleException::class);

        new class($this->app) extends Module {
            protected $name = 'Missing Key';

            protected function config(): array
            {
                return [];
            }
        };
    }

    public function testRequiresName()
    {
        $this->expectException(InvalidModuleException::class);

        new class($this->app) extends Module {
            protected $key = 'missing_name';

            protected function config(): array
            {
                return [];
            }
        };
    }

    public function testBootSetsConfig()
    {
        $module = new PostsModule($this->app);
        $module->boot();

        $this->assertInstanceOf(ModuleConfig::class, $module->getConfig());
        $this->assertEquals(__DIR__.'/stubs/Posts', $module->get('paths.module'));
        $this->assertEquals('Handles posts', $module->get('description'));
        $this->assertEquals(1.1, $module->get('version'));
        $this->assertEquals('Migrations', $module->get('paths.migrations'));
    }

    public function testBootOverridesModuleConfig()
    {
        $module = new class($this->app) extends Module {
            protected $key = 'foobar';

            protected $name = 'FooBar';

            protected function config(): array
            {
                return [
                    'test' => 123,
                ];
            }
        };

        $module->boot(['test' => 'newval']);

        $this->assertEquals('newval', $module->get('test'));
    }

    public function testGetNamespace()
    {
        $module = new PostsModule($this->app);
        $module->boot();

        $this->assertEquals('Modular\Tests\stubs\Posts', $module->getNamespace());
        $this->assertEquals('Modular\Tests\stubs\Posts\Models', $module->getNamespace('models'));
    }

    public function testGetPath()
    {
        $module = new PostsModule($this->app);
        $module->boot();

        $this->assertEquals(__DIR__.'/stubs/Posts', $module->getPath());
        $this->assertEquals(__DIR__.'/stubs/Posts/Http/Controllers', $module->getPath('controllers'));
        $this->assertEquals('Http/Controllers', $module->getPath('controllers', true));
    }

    public function test__getGetsFromConfig()
    {
        $module = new PostsModule($this->app);
        $module->boot();

        $this->assertNull($module->foo_bar_123);
    }
}
