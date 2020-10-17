<?php

namespace Modular\Tests;

use Modular\Module;

class ModuleTest extends BaseTestCase
{
    protected $loadModules = ['base'];

    public function test_set_default_config()
    {
        $module = new Module;
        $ret = $module->setDefaultConfig('foo_bar');

        $this->assertInstanceOf(Module::class, $ret);

        $this->assertEquals('Database/Migrations', $module['paths.migrations']);
        $this->assertEquals('foo_bar', $module['key']);
        $this->assertEquals('FooBar', $module['name']);
        $this->assertEquals('App\\FooBar', $module['namespace']);

        $module->setDefaultConfig('base');
        $this->assertEquals('Base', $module['namespace']);
    }

    public function test_class_fully_namespaced()
    {
        $module = new Module;
        $module->setDefaultConfig('foo');
        $module['paths.bar'] = 'Bar';

        $this->assertEquals('App\\Foo\\Bar\\Baz', $module->classFullyNamespaced('Baz', 'bar'));

        $this->assertEquals('App\\Foo\\Bar\\Baz', $module->classFullyNamespaced('App\\Foo\\Bar\\Baz', 'bar'));

        $this->assertEquals('App\\Foo\\Bar\\Baz\\Qux', $module->classFullyNamespaced('Baz\\Qux', 'bar'));
    }
}
