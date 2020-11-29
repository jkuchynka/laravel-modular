<?php

namespace Modular\Tests\Concerns;

use Illuminate\Support\Facades\Route;
use Modular\Exceptions\InvalidModuleException;
use Modular\Tests\BaseTestCase;
use Modular\Tests\stubs\Posts\PostsModule;

class LoadsRoutesTest extends BaseTestCase
{
    protected $module;

    protected $ns = 'Modular\Tests\stubs\Posts';

    protected function setUp(): void
    {
        parent::setUp();
        $this->module = new PostsModule($this->app);
        $this->module->boot();
        $this->module->loadRoutes();
    }

    public function testInvalidRouteGroupThrowsException()
    {
        $this->module->getConfig()->push('routes', [
            [
                // No child routes
            ]
        ]);

        $this->expectException(InvalidModuleException::class);

        $this->module->loadRoutes();
    }

    public function testLoadsResourceRoutes()
    {
        $routeCollection = Route::getRoutes();
        $route = $routeCollection->getByName('posts.index');

        $this->assertEquals('posts', $route->uri);
        $this->assertEquals([
            'middleware' => 'web',
            'as' => 'posts.index',
            'uses' => $this->ns.'\Http\Controllers\PostController@index',
            'controller' => $this->ns.'\Http\Controllers\PostController@index',
            'namespace' => null,
            'prefix' => '',
            'where' => []
        ], $route->action);

        $route = $routeCollection->getByName('posts.create');

        $this->assertEquals('posts/create', $route->uri);
        $this->assertEquals([
            'middleware' => 'web',
            'as' => 'posts.create',
            'uses' => $this->ns.'\Http\Controllers\PostController@create',
            'controller' => $this->ns.'\Http\Controllers\PostController@create',
            'namespace' => null,
            'prefix' => '',
            'where' => []
        ], $route->action);

        $route = $routeCollection->getByName('posts.show');

        $this->assertEquals('posts/{post}', $route->uri);
        $this->assertEquals([
            'middleware' => 'web',
            'as' => 'posts.show',
            'uses' => $this->ns.'\Http\Controllers\PostController@show',
            'controller' => $this->ns.'\Http\Controllers\PostController@show',
            'namespace' => null,
            'prefix' => '',
            'where' => []
        ], $route->action);

        $route = $routeCollection->getByName('posts.edit');

        $this->assertEquals('posts/{post}/edit', $route->uri);
        $this->assertEquals([
            'middleware' => 'web',
            'as' => 'posts.edit',
            'uses' => $this->ns.'\Http\Controllers\PostController@edit',
            'controller' => $this->ns.'\Http\Controllers\PostController@edit',
            'namespace' => null,
            'prefix' => '',
            'where' => []
        ], $route->action);

        $route = $routeCollection->getByName('posts.update');

        $this->assertEquals('posts/{post}', $route->uri);
        $this->assertEquals([
            'middleware' => 'web',
            'as' => 'posts.update',
            'uses' => $this->ns.'\Http\Controllers\PostController@update',
            'controller' => $this->ns.'\Http\Controllers\PostController@update',
            'namespace' => null,
            'prefix' => '',
            'where' => []
        ], $route->action);

        $route = $routeCollection->getByName('posts.destroy');

        $this->assertEquals('posts/{post}', $route->uri);
        $this->assertEquals([
            'middleware' => 'web',
            'as' => 'posts.destroy',
            'uses' => $this->ns.'\Http\Controllers\PostController@destroy',
            'controller' => $this->ns.'\Http\Controllers\PostController@destroy',
            'namespace' => null,
            'prefix' => '',
            'where' => []
        ], $route->action);
    }

    public function testLoadsApiResourceRoutes()
    {
        $routeCollection = Route::getRoutes();

        $route = $routeCollection->getByName('posts.reports.index');

        $this->assertEquals('api/posts/reports', $route->uri);
        $this->assertEquals([
            'middleware' => ['api'],
            'as' => 'posts.reports.index',
            'uses' => $this->ns.'\Http\Controllers\ReportController@index',
            'controller' => $this->ns.'\Http\Controllers\ReportController@index',
            'namespace' => null,
            'prefix' => 'api/posts',
            'where' => []
        ], $route->action);
    }

    public function testLoadsSingleRoute()
    {
        $routeCollection = Route::getRoutes();

        $route = $routeCollection->getByName('posts.download');

        $this->assertEquals('api/posts/{post}/download', $route->uri);
        $this->assertEquals([
            'middleware' => ['api'],
            'as' => 'posts.download',
            'uses' => $this->ns.'\Http\Controllers\ReportController@download',
            'controller' => $this->ns.'\Http\Controllers\ReportController@download',
            'namespace' => null,
            'prefix' => 'api',
            'where' => []
        ], $route->action);
    }
}
