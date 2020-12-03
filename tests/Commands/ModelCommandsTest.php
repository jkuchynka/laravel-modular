<?php

namespace Modular\Tests\Commands;

class ModelCommandsTest extends CommandsTestCase
{
    public function testModelMakeCommand()
    {
        $this->artisan('make:model', [
            'module' => 'foo_bar',
            'name' => 'FooBar'
        ]);

        $contents = $this->assertCommandPath('Models/FooBar.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Models;', $contents);
        $this->assertStringContainsString('use Base\\Model;', $contents);
    }

    public function testModelMakeCommandAll()
    {
        $this->artisan('make:model', [
            'module' => 'foo_bar',
            'name' => 'FooBar',
            '--all' => true
        ]);

        // Factory
        $contents = $this->assertCommandPath('Database/Factories/FooBarFactory.php');

        $this->assertStringContainsString('use VFS\\FooBar\\Models\\FooBar', $contents);
        $this->assertStringContainsString('define(FooBar', $contents);

        // Migration
        $dir = $this->assertCommandPath('Database/Migrations');

        $file = $dir->getChildren()[0];
        $contents = $file->getContent();

        $this->assertStringContainsString('create_foo_bars_table', $file->getName());

        // Seeder
        $contents = $this->assertCommandPath('Database/Seeds/FooBarSeeder.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Database\\Seeds;', $contents);
        $this->assertStringContainsString('class FooBarSeeder ', $contents);

        // Controller
        $contents = $this->assertCommandPath('Http/Controllers/FooBarController.php');

        $this->assertStringContainsString('namespace VFS\\FooBar\\Http\\Controllers;', $contents);
        $this->assertStringContainsString('class FooBarController ', $contents);
        $this->assertStringContainsString('use VFS\\FooBar\\Models\\FooBar;', $contents);
        $this->assertStringContainsString('FooBar $fooBar', $contents);
    }
}
