<?php

namespace Modular\Tests\Routes;

use Modular\Exceptions\InvalidRouteException;
use Modular\Routes\RouteBuilder;
use Modular\Tests\BaseTestCase;
use Modular\Tests\stubs\Posts\PostsModule;

class RouteBuilderTest extends BaseTestCase
{
    protected $module;

    protected function setUp(): void
    {
        parent::setUp();
        $this->module = new PostsModule($this->app);
        $this->module->boot();
    }

    public function testValidatesMissingUri()
    {
        $builder = new RouteBuilder($this->module, ['method' => 'resource']);
        $this->expectException(InvalidRouteException::class);
        $builder->getUri();
    }

    public function testGetMethod()
    {
        $builder = new RouteBuilder($this->module, ['uri' => '', 'method' => 'post']);
        $this->assertEquals('post', $builder->getMethod());
    }

    public function testValidatesInvalidMethod()
    {
        $builder = new RouteBuilder($this->module, ['uri' => '', 'method' => 'foobar']);
        $this->expectException(InvalidRouteException::class);
        $builder->getMethod();
    }

    public function testMethodDefaultsToGet()
    {
        $builder = new RouteBuilder($this->module, ['uri' => '']);
        $this->assertEquals('get', $builder->getMethod());
    }

    public function testNamePrefixedByModule()
    {
        $builder = new RouteBuilder($this->module, ['uri' => '', 'name' => 'foobar']);
        $this->assertEquals('posts.foobar', $builder->getName());
    }

    public function testNameDefaultUsesUri()
    {
        $builder = new RouteBuilder($this->module, ['uri' => 'foo/foo-bar']);
        $this->assertEquals('posts.foo.fooBar', $builder->getName());
    }

    public function testUsesDefaultControllerAndMethod()
    {
        $builder = new RouteBuilder($this->module, ['uri' => '']);
        $this->assertEquals('PostController@posts', $builder->getUses());
    }

    public function testUsesDefaultController()
    {
        $builder = new RouteBuilder($this->module, ['uri' => '', 'uses' => 'foobar']);
        $this->assertEquals('PostController@foobar', $builder->getUses());
    }

    public function testUsesDefaultControllerAndUri()
    {
        $builder = new RouteBuilder($this->module, ['uri' => 'foo/bar', 'uses' => 'FooBarController']);
        $this->assertEquals('FooBarController@fooBar', $builder->getUses());
    }

    public function testUsesDefaultControllerAndUriWithoutUses()
    {
        $builder = new RouteBuilder($this->module, ['uri' => 'foo/bar']);
        $this->assertEquals('PostController@fooBar', $builder->getUses());
    }

    public function testValidatesInvalidUses()
    {
        $builder = new RouteBuilder(
            $this->module,
            ['uri' => '', 'method' => 'resource', 'uses' => 'FooController@bar']
        );
        $this->expectException(InvalidRouteException::class);
        $builder->getUses();
    }

    public function testBuildsResource()
    {
        $builder = new RouteBuilder($this->module, [
            'uri' => '', 'method' => 'resource'
        ]);
        $this->assertTrue($builder->isResource());
        $this->assertEquals('resource', $builder->getMethod());
    }

    public function testBuildsApiResource()
    {
        $module = new PostsModule($this->app);
        $builder = new RouteBuilder($module, [
            'uri' => '', 'method' => 'api-resource'
        ]);
        $this->assertTrue($builder->isResource());
        $this->assertEquals('api-resource', $builder->getMethod());
    }
}
