<?php

namespace Base\Tests\Unit;

use Adbar\Dot;
use Base\Exceptions\ModuleNotFoundException;
use Illuminate\Config\Repository;
use Symfony\Component\Yaml\Yaml;
use org\bovigo\vfs\vfsStream;
use Base\Modules\ModulesService;

class ModulesServiceTest extends \Base\Tests\TestCase
{
    protected $loadModules = ['base'];

    public function test_get_enabled_modules()
    {
        $modulesService = new ModulesService;
        $enabled = $modulesService->getEnabledModules();
        $this->assertEquals(['base'], $enabled);
        $modulesService->loadModules($enabled);
        $modules = $modulesService->getModules();
        $this->assertIsArray($modules);
        $this->assertInstanceOf(Dot::class, $modules['base']);
    }

    public function test_get_modules_path()
    {
        // Default
        $modulesService = new ModulesService;
        $this->assertEquals(base_path().'/app', $modulesService->getModulesPath());

        $options = [
            ['modules', base_path().'/modules'],
            ['/modules', '/modules'],
            ['vfs://root/modules', 'vfs://root/modules']
        ];
        foreach ($options as $option) {
            $repo = new Repository([
                'modules' => [
                    'paths' => [
                        'modules' => $option[0]
                    ]
                ]
            ]);
            $modulesService = new ModulesService($repo);
            $this->assertEquals($option[1], $modulesService->getModulesPath());
        }
    }

    public function test_get_non_existent_module_throws_exception()
    {
        $this->expectException(ModuleNotFoundException::class);
        $modulesService = new ModulesService;
        $modulesService->getModule('baz_qux');
    }

    public function test_load_modules()
    {
        $repo = new Repository([
            'modules' => [
                'modules' => [
                    'foo' => [],
                    'bar' => [],
                    'baz' => []
                ]
            ]
        ]);
        $modulesService = new ModulesService($repo);
        $modulesService->loadModules(['foo', 'bar', 'baz']);
        $modules = $modulesService->getModules();

        $this->assertEquals('foo', $modules['foo']['key']);
        $this->assertEquals('Bar', $modules['bar']['name']);
        $this->assertEquals('App\\Baz', $modules['baz']['namespace']);
    }

    public function test_load_modules_with_yaml_config()
    {
        $root = vfsStream::setup();
        $repo = new Repository([
            'modules' => [
                'modules' => [
                    'foo' => [
                        'paths' => [
                            'module' => $root->url()
                        ]
                    ]
                ]
            ]
        ]);
        vfsStream::newFile('foo.config.yml')
            ->withContent('
version: 123
description: foobar
paths:
    migrations: db-migrations
                ')
            ->at($root);
        $modulesService = new ModulesService($repo);
        $modulesService->loadModules(['foo']);
        $foo = $modulesService->getModule('foo');
        $this->assertEquals('123', $foo['version']);
        $this->assertEquals('foobar', $foo['description']);
        $this->assertEquals('db-migrations', $foo['paths.migrations']);
    }

    public function test_load_modules_with_app_config()
    {
        $root = vfsStream::setup();
        $repo = new Repository([
            'modules' => [
                'modules' => [
                    'foo' => [
                        'version' => '123.123',
                        'paths' => [
                            'module' => $root->url(),
                            'migrations' => 'Database',
                            'controllers' => 'CTRL'
                        ]
                    ]
                ]
            ]
        ]);
        vfsStream::newFile('foo.config.yml')
            ->withContent('
version: 123
description: foobar
paths:
    migrations: db-migrations
                ')
            ->at($root);
        $modulesService = new ModulesService($repo);
        $modulesService->loadModules(['foo']);
        $foo = $modulesService->getModule('foo');
        $this->assertEquals('123.123', $foo['version']);
        $this->assertEquals('Database', $foo['paths.migrations']);
        $this->assertEquals('CTRL', $foo['paths.controllers']);
    }

    public function test_load_module_routes_from_yaml()
    {
        $yaml = '
routes:
    -
        route: foo
        method: get
        controller: FooController
';
        $root = vfsStream::setup('root', null, [
            'Foo' => [
                'foo.config.yaml' => $yaml
            ]
        ]);
        $repo = new Repository([
            'modules' => [
                'paths' => [
                    'modules' => $root->url()
                ],
                'modules' => [
                    'foo' => []
                ]
            ]
        ]);

        $modulesService = new ModulesService($repo);
        $modulesService->loadModules(['foo']);

        $foo = $modulesService->getModule('foo');
        $this->assertEquals('foo', $foo['routes.0.route']);
    }

    public function test_load_module_dependencies()
    {
        $repo = new Repository([
            'modules' => [
                'modules' => [
                    'foo' => [
                        'dependsOn' => ['bar']
                    ]
                ]
            ]
        ]);
        $modulesService = new ModulesService($repo);
        $modulesService->loadModules(['foo']);
        $modules = $modulesService->getModules();
        $this->assertInstanceOf(Dot::class, $modules['bar']);
    }

    public function test_load_module_sets_module_path()
    {
        $repo = new Repository([
            'modules' => [
                'modules' => [
                    'foo_bar' => []
                ]
            ]
        ]);
        $modulesService = new ModulesService($repo);
        $modulesService->loadModules(['foo_bar']);
        $modules = $modulesService->getModules();
        $this->assertEquals(base_path().'/app/FooBar', $modules['foo_bar']['paths.module']);
    }
}
