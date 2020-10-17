<?php

namespace Blue\Tests\Unit;

use Adbar\Dot;
use Illuminate\Support\Facades\Route;
use Base\Exceptions\InvalidRouteException;
use Base\Providers\ModulesRouteServiceProvider;
use Base\Modules\Module;
use Base\Modules\ModulesService;
use Base\Modules\ModulesRoutes;

class ModulesRoutingTest extends \Base\Tests\TestCase
{
    protected $loadModules = ['base'];

    protected function setUp(): void
    {
        // Disable loading module routes
        ModulesRouteServiceProvider::setLoadRoutes(false);
        parent::setUp();
        // Re-enable route loading
        ModulesRouteServiceProvider::setLoadRoutes(true);
    }

    public function test_config_route_sets_uri()
    {
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $routes = new ModulesRoutes;

        $options = [
            [['uri' => ''], 'foo'],
            [['uri' => 'bar'], 'foo/bar'],
            [['uri' => '/bar'], '/bar'],
            [['uri' => 'bar/baz/qux'], 'foo/bar/baz/qux']
        ];

        foreach ($options as $option) {
            $route = $routes->routeConfig($option[0], $foo);
            $this->assertEquals($option[1], $route['uri']);
        }
    }

    public function test_config_route_sets_method()
    {
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $routes = new ModulesRoutes;

        $options = [
            [['uri' => ''], 'get'],
            [['uri' => '', 'method' => 'get'], 'get'],
            [['uri' => '', 'method' => 'GET'], 'get'],
            [['uri' => '', 'method' => 'post'], 'post']
        ];

        foreach ($options as $option) {
            $route = $routes->routeConfig($option[0], $foo);
            $this->assertEquals($option[1], $route['method']);
        }
    }

    public function test_config_route_sets_uses()
    {
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $routes = new ModulesRoutes;

        $options = [
            [['uri' => '', 'uses' => 'FooBarController@test'], 'App\\Foo\\Http\\Controllers\\FooBarController@test'],
            [['uri' => '', 'uses' => 'test'], 'App\\Foo\\Http\\Controllers\\FooController@test'],
            [['uri' => 'foo/bar'], 'App\\Foo\\Http\\Controllers\\FooController@fooBar']
        ];

        foreach ($options as $option) {
            $route = $routes->routeConfig($option[0], $foo);
            $this->assertEquals($option[1], $route['uses']);
        }

        $foo['routesController'] = 'FooBarController';
        $routes = new ModulesRoutes;
        $route = $routes->routeConfig(['uri' => ''], $foo);
        $this->assertEquals('App\\Foo\\Http\\Controllers\\FooBarController@foo', $route['uses']);

        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $foo['paths.controllers'] = '';
        $routes = new ModulesRoutes;

        $route = $routes->routeConfig(['uri' => ''], $foo);
        $this->assertEquals('App\\Foo\\FooController@foo', $route['uses']);

        $route = $routes->routeConfig(['uri' => '', 'uses' => 'App\\Bar\\BarController'], $foo);
        $this->assertEquals('App\\Bar\\BarController@foo', $route['uses']);
    }

    public function test_config_route_sets_name()
    {
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $routes = new ModulesRoutes;

        $options = [
            [['uri' => ''], 'foo'],
            [['uri' => '{foo}'], 'foo'],
            [['uri' => '', 'method' => 'post'], 'foo'],
            [['uri' => '{foo}', 'method' => 'put'], 'foo'],
            [['uri' => '{foo}', 'method' => 'delete'], 'foo'],
            [['uri' => '', 'method' => 'delete'], 'foo'],
            [['uri' => '', 'name' => 'foobar'], 'foo.foobar'],
            [['uri' => 'bar'], 'foo.bar'],
            [['uri' => 'bar', 'method' => 'resource'], 'foo.bar']
        ];

        foreach ($options as $option) {
            $route = $routes->routeConfig($option[0], $foo);
            $this->assertEquals($option[1], $route['name']);
        }
    }

    public function test_config_handles_resource_method()
    {
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $routes = new ModulesRoutes;
        $route = $routes->routeConfig([
            'uri' => '',
            'method' => 'resource'
        ], $foo);

        $this->assertEquals('resource', $route['method']);
        $this->assertEquals('foo', $route['name']);
        $this->assertEquals('App\\Foo\\Http\\Controllers\\FooController', $route['uses']);

        $route = $routes->routeConfig([
            'uri' => '/foobar',
            'method' => 'resource',
            'uses' => 'FooBarController'
        ], $foo);

        $this->assertEquals('resource', $route['method']);
        $this->assertEquals('foo', $route['name']);
        $this->assertEquals('App\\Foo\\Http\\Controllers\\FooBarController', $route['uses']);
    }

    public function test_loads_resource_routes()
    {
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $foo['routes'] = [
            [
                'uri' => '',
                'method' => 'resource'
            ]
        ];
        $config = ['foo' => $foo];
        $routes = new ModulesRoutes($config);
        $routes->loadRoutes();

        $routeCollection = Route::getRoutes();
        $route = $routeCollection->getByName('foo.index');

        $this->assertInstanceOf(\Illuminate\Routing\Route::class, $route);
    }

    public function test_invalid_resource_config_throws_exception()
    {
        $this->expectException(InvalidRouteException::class);
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        $foo['routes'] = [
            [
                'uri' => '',
                'method' => 'resource',
                'uses' => 'FooBarController@test'
            ]
        ];
        $config = ['foo' => $foo];
        $routes = new ModulesRoutes($config);
        $routes->loadRoutes();
    }

    public function test_invalid_config_throws_exception()
    {
        $this->expectException(InvalidRouteException::class);
        $foo = new Module;
        $foo->setDefaultConfig('foo');
        // Route is missing required uri
        $foo['routes'] = [
            [
                'name' => 'foo'
            ]
        ];
        $config = ['foo' => $foo];
        $routes = new ModulesRoutes($config);
        $routes->loadRoutes();
    }

    /**
     * @todo
     */
    public function _test_route_collision_throws_exception()
    {

    }
}
