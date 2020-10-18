<?php

namespace Modular\Tests\stubs\Posts;

use Modular\Concerns\LoadsRoutes;
use Modular\Module;

class PostsModule extends Module
{
    use LoadsRoutes;

    protected $key = 'posts';

    protected $name = 'Posts';

    protected function config() : array
    {
        return [];
    }

    protected function routes(): array
    {
        return [
            // This should create all resource routes (index, store, show, update, destroy)
            // with a prefix of the module key, routed to the default module controller
            // e.g. GET posts/{post} -> PostController@show
            ['uri' => '', 'method' => 'resource'],
        ];
    }
}
