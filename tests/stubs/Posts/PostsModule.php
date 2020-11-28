<?php

namespace Modular\Tests\stubs\Posts;

use Modular\Concerns\LoadsRoutes;
use Modular\Module;

class PostsModule extends Module
{
    use LoadsRoutes;

    protected $key = 'posts';

    protected $name = 'Posts';

    protected $description = 'Handles posts';

    protected $version = 1.1;

    protected function config() : array
    {
        return [
            'paths' => [
                'migrations' => 'Migrations',
            ],
            'namespace' => 'Modular\Tests\stubs\Posts',
        ];
    }

    protected function routes(): array
    {
        return [
            [
                'routes' => [
                    // This should create all resource routes (index, store, show, update, destroy)
                    // with a prefix of the module key, routed to the default module controller
                    // e.g. GET posts/{post} -> PostController@show
                    ['uri' => '', 'method' => 'resource'],
                ]
            ],
            [
                'prefix' => 'api',
                'middleware' => ['api'],
                'routes' => [
                    ['uri' => 'reports', 'method' => 'api-resource', 'uses' => 'ReportController'],
                    ['uri' => '{post}/download', 'uses' => 'ReportController@download']
                ]
            ]
        ];
    }
}
