# Laravel Modular

Requires: Laravel 8.0^

This is a WIP and not suitable for production use yet.

This package was created to make it easier to use Laravel in a modular setup. You can use a modular pattern with out of the box Laravel, but will run into issues and annoyances, such as:

- Console commands won't map to your modular directory structure
- Can't cleanly separate migrations, factories, seeders, routes or views

A module is setup with the default Laravel directory structure, but only provides functionality related to the module. It can publish a config that can be modified by other modules or at the global level.

## Installation

```
composer require jkuchynka/laravel-modular
php artisan vendor:publish --provider="Modular\ModularServiceProvider"
```

This package can be used in existing or new Laravel apps. If you want a fresh starting point, you can delete all the files in app/, that comes with a default Laravel installation. Modular provides this as a built-in module called "Base". You will need to edit a few files for Laravel to work correctly:

```php
// bootstrap/app.php
$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    Modular\Base\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    Modular\Base\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Modular\Base\Exceptions\Handler::class
);
```

```php
// config/app.php
/*
 * Application Service Providers...
 */
Modular\Base\Providers\AppServiceProvider::class,
Modular\Base\Providers\AuthServiceProvider::class,
// Modular\Base\Providers\BroadcastServiceProvider::class,
Modular\Base\Providers\EventServiceProvider::class,
Modular\Base\Providers\RouteServiceProvider::class,
```

There's nothing special happening in the Modular\Base module, so you can safely create your own App\Base module and pull in these files as needed if you need to change them.

## Artisan

All of the make commands work similar to default Laravel, but come with a module param, which creates the file in the right modular directory with the right namespaces etc. Pass the module name as the first param after each make command.

```
php artisan make:model Users User
```

### Coming Soon

A make:module command is in the works, which will create a new module in app/. This will be interactive so you can setup models, controllers, requests, etc... in one command. The new module directory structure can be modified by config/modular.php. 

Custom stubs are also in the works. Use your own custom stubs and plug them into make commands.

## Module

A module can simply be a directory in app/, with a NameModule.php class. This must extend Modular/Module and provide some required properties and methods. This module must be enabled in config/modular.php by adding the namespaced module with an array of config variables to overwrite.

```php
return [
    'modules' => [
        \App\Posts\PostsModule::class => [
            'some_var' => 'foo'
        ]
    ]
]
```

```php
<?php

namespace App\Posts;

use Modular\Module;

class PostsModule extends Module
{
    // The unique key of the module
    protected $key = 'posts';

    // The display name of the module
    protected $name = 'Posts';

    protected $description = 'Posts Module!!!';

    protected $version = '1.0';

    // Module config, can be overridden by other modules and app
    public function config(): array
    {
        return [
            'some_config_var' => true,
            'dependsOn' => ['users']
        ];
    }

    // Module routes
    public function routes(): array
    {
        return [
            // This is a route group
            [
                'prefix' => 'api',
                'middleware' => ['api'],
                'routes' => [
                    ['uri' => '', 'method' => 'api-resource']
                ]
            ]   
        ];       
    }   
}
```

### Config

A module can provide its own config, which can be modified by other modules or the global app in config/modular.php. This can be useful to have flexible modules that can satisfy numerous use cases. For example, an Auth module can by default provide login by password, Google and Facebook, and you can specify which login methods to use in config/modular.php.

Examples for using a module config:
```php
$modular = app(\Modular\Modular::class);
$users = $modular->getModule('users');

// Get module name
echo $users->name;

// Get all config vars
dump($users->getConfig());

// Get a config var with default
echo $users->get('some_var', 'foo');
```

### Routes

In the Module class, a module can provide routes. This method should return an array of groups, with child routes. A group defaults to a web route. Each child route is prefixed by the module key, unless the URI starts with a /, which considers the route as absolute. In this example, uses is omitted, so it would use the default module controller, e.g. PostController


```php
public function routes(): array
{
    return [
        [
            'prefix' => 'api',
            'middleware' => ['api'],
            'routes' => [
                ['uri' => '', 'method' => 'resource']
            ]
        ]
    ];
}
```
