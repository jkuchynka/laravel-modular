<?php

namespace Modular\Tests;

use Modular\ModuleConfig;

class ModuleConfigTest extends BaseTestCase
{
    public function testModuleConfig()
    {
        $config = new ModuleConfig('foo_bar');

        $this->assertEquals('foo_bar', $config['key']);
        $this->assertEquals('FooBar', $config['name']);
    }
}
