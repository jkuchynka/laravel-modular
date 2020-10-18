<?php

namespace Modular\Tests;

use Modular\Exceptions\InvalidModuleException;
use Modular\ModuleConfig;
use Modular\Tests\stubs\InvalidNoKeyModule;
use Modular\Tests\stubs\InvalidNoNameModule;
use Modular\Tests\stubs\Posts\PostsModule;

class ModuleTest extends BaseTestCase
{
    public function testRequiresKey()
    {
        $this->expectException(InvalidModuleException::class);

        new InvalidNoKeyModule($this->app);
    }

    public function testRequiresName()
    {
        $this->expectException(InvalidModuleException::class);

        new InvalidNoNameModule($this->app);
    }

    public function testBootSetsConfig()
    {
        $module = new PostsModule($this->app);
        $module->boot();

        $this->assertInstanceOf(ModuleConfig::class, $module->getConfig());
    }

//    public function test_set_default_config()
//    {
//        $module = new Module;
//        $ret = $module->setDefaultConfig('foo_bar');
//
//        $this->assertInstanceOf(Module::class, $ret);
//
//        $this->assertEquals('Database/Migrations', $module['paths.migrations']);
//        $this->assertEquals('foo_bar', $module['key']);
//        $this->assertEquals('FooBar', $module['name']);
//        $this->assertEquals('App\\FooBar', $module['namespace']);
//
//        $module->setDefaultConfig('base');
//        $this->assertEquals('Base', $module['namespace']);
//    }
//
//    public function test_class_fully_namespaced()
//    {
//        $module = new Module;
//        $module->setDefaultConfig('foo');
//        $module['paths.bar'] = 'Bar';
//
//        $this->assertEquals('App\\Foo\\Bar\\Baz', $module->classFullyNamespaced('Baz', 'bar'));
//
//        $this->assertEquals('App\\Foo\\Bar\\Baz', $module->classFullyNamespaced('App\\Foo\\Bar\\Baz', 'bar'));
//
//        $this->assertEquals('App\\Foo\\Bar\\Baz\\Qux', $module->classFullyNamespaced('Baz\\Qux', 'bar'));
//    }
}
