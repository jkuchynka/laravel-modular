<?php

namespace Modular\Tests\Support;

use Modular\Support\Namespaces;
use Modular\Tests\BaseTestCase;

class NamespacesTest extends BaseTestCase
{
    public function testFromPath()
    {
        $this->assertEquals('Foo\Http\Controllers', Namespaces::fromPath('Foo/Http/Controllers/FooController.php'));
        $this->assertEquals('Foo\Http\Controllers', Namespaces::fromPath('Foo/Http/Controllers'));
    }

    public function testCombine()
    {
        $this->assertEquals('Foo\Http\Controllers', Namespaces::combine('Foo', 'Http\Controllers'));
        $this->assertEquals('\Foo\Bar\Baz', Namespaces::combine('\Foo\Bar\\', 'Baz\\'));
        $this->assertEquals('Foo\Bar', Namespaces::combine('Foo\Bar', ''));
    }

    public function testToPath()
    {
        $this->assertEquals('Foo/Http/Controllers', Namespaces::toPath('Foo\Http\Controllers'));
        $this->assertEquals('Foo/Http/Controllers', Namespaces::toPath('Foo\Http\Controllers\FooController', true));
    }
}
