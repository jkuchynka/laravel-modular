<?php

namespace Base\Tests\Feature\Console;

class ControllerCommandsTest extends CommandsTestCase
{
    public function test_controller_make_command()
    {
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooBarController'
        ]);

        $contents = $this->assertCommandPath('Http/Controllers/FooBarController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('class FooBarController ', $contents);
    }

    public function test_controller_make_command_nested()
    {
        // Nested
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooBarNestedController',
            '--model' => 'FooBar',
            '--parent' => 'Foo',
            '--no-interaction' => true
        // @todo: Should be better way to test this,
        // --no-interaction doesn't seem to work here
        ])
            ->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', false)
            ->expectsQuestion('A VFS\FooBar\Models\FooBar model does not exist. Do you want to generate it?', false);


        $contents = $this->assertCommandPath('Http/Controllers/FooBarNestedController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('class FooBarNestedController ', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\FooBar;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo;', $contents);
    }

    public function test_controller_make_command_nested_creates_parent_model()
    {
        // Nested
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooBarNestedController',
            '--model' => 'FooBar',
            '--parent' => 'Foo'
        ])
            ->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', true)
            ->expectsQuestion('A VFS\FooBar\Models\FooBar model does not exist. Do you want to generate it?', true);

        $contents = $this->assertCommandPath('Models/Foo.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Models;', $contents);
        $this->assertStringContainsString('class Foo ', $contents);

        $contents = $this->assertCommandPath('Models/FooBar.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Models;', $contents);
        $this->assertStringContainsString('class FooBar ', $contents);
    }

    public function test_controller_make_command_model()
    {
        // controller.model.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--model' => 'Foo',
            '--no-interaction' => true
        // @todo: Should be better way to test this,
        // --no-interaction doesn't seem to work here
        ])->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', false);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo;', $contents);
        $this->assertStringContainsString('Foo $foo', $contents);
    }

    public function test_controller_make_command_model_creates_model()
    {
        // controller.model.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--model' => 'Foo'
        ])->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', true);

        $contents = $this->assertCommandPath('Models/Foo.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Models;', $contents);
        $this->assertStringContainsString('class Foo', $contents);
    }

    public function test_controller_make_command_invokable()
    {
        // controller.invokable.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--invokable' => true
        ]);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
    }

    public function test_controller_make_command_api()
    {
        // controller.api.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--api' => true,
        ]);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
    }

    public function test_controller_make_command_nested_api()
    {
        // controller.nested.api.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--parent' => 'Foo',
            '--no-interaction' => true
        ])
            ->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', false);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Model;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
        $this->assertStringContainsString('Foo $foo, Model $model', $contents);
    }

    public function test_controller_make_command_model_api()
    {
        // controller.model.api.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--model' => 'Foo',
            '--api' => true,
            '--no-interaction' => true
        ])->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', false);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
        $this->assertStringContainsString('Foo $foo', $contents);
    }

    public function test_controller_make_command_api_querybuilder()
    {
        // controller.api.querybuilder.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--api' => true,
            '--querybuilder' => true,
            '--no-interaction' => true
        ]);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Model;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
    }

    public function test_controller_make_command_model_api_querybuilder()
    {
        // controller.model.api.querybuilder.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--model' => 'Foo',
            '--api' => true,
            '--querybuilder' => true,
            '--no-interaction' => true
        ])->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', false);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
        $this->assertStringContainsString('Foo $foo', $contents);
    }

    public function test_controller_make_command_nested_api_querybuilder()
    {
        // controller.nested.api.querybuilder.stub
        $this->artisan('make:controller', [
            'module' => 'foo_bar',
            'name' => 'FooController',
            '--model' => 'FooBar',
            '--parent' => 'Foo',
            '--api' => true,
            '--querybuilder' => true,
            '--no-interaction' => true
        ])
            ->expectsQuestion('A VFS\FooBar\Models\Foo model does not exist. Do you want to generate it?', false)
            ->expectsQuestion('A VFS\FooBar\Models\FooBar model does not exist. Do you want to generate it?', false);

        $contents = $this->assertCommandPath('Http/Controllers/FooController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\FooBar;', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\Foo;', $contents);
        $this->assertStringContainsString('class FooController ', $contents);
        $this->assertStringContainsString('Foo $foo, FooBar $fooBar', $contents);
    }
}
