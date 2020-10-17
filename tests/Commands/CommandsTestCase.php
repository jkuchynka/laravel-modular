<?php

namespace Base\Tests\Feature\Console;

use Base\Modules\ModulesService;
use Base\Tests\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use Illuminate\Config\Repository;
use Symfony\Component\Yaml\Yaml;

$module = Modular\Modular::module('users');

$module->get('name')

// Autoloads classes in VFS namespace from vfsStream filesystem
spl_autoload_register(function ($class) {
    if (preg_match('/^VFS\\\(.*)$/', $class, $matches)) {
        @include('vfs://root/'.str_replace('\\', '/', $matches[1]).'.php');
    }
});

abstract class CommandsTestCase extends TestCase
{
    protected $loadModules = ['base', 'foo_bar'];

    protected static $root;

    static public function setUpBeforeClass(): void
    {
        static::$root = vfsStream::setup();
    }

    protected function beforeTest()
    {
        // Clear the VFS filesystem
        if (static::$root->hasChild('FooBar')) {
            static::$root->removeChild('FooBar');
        }

        // Rebind modulesService to our own with custom config
        $this->app->singleton('modules', function ($app) {
            $config = $app->make(Repository::class);
            $config->set('modules', [
                'modules' => [
                    'base' => [],
                    'foo_bar' => [
                        'paths' => [
                            'module' => vfsStream::url('root/FooBar')
                        ],
                        'namespace' => 'VFS\\FooBar'
                    ]
                ]
            ]);
            $yaml = $app->make(Yaml::class);
            $modulesService = new ModulesService($config, $yaml);
            $modulesService->loadModules($modulesService->getEnabledModules());

            return $modulesService;
        });
    }

    static public function tearDownAfterClass(): void
    {
        static::$root = null;
    }

    protected function debugVfsStructure()
    {
        $visitor = new vfsStreamStructureVisitor;
        $visitor->visitDirectory(static::$root);
        print_r($visitor->getStructure());
    }

    /**
     * Assert that the command generates a file at a path
     * and return file contents.
     *
     * @param  string $path
     * @return mixed
     */
    protected function assertCommandPath($path)
    {
        $path = 'FooBar/'.$path;
        if (! static::$root->hasChild($path)) {
            $visitor = new vfsStreamStructureVisitor;
            $visitor->visitDirectory(static::$root);
            $this->fail('Command expected new class at path: '.$path.', filesystem: '.print_r($visitor->getStructure(), true));
        }
        $child = static::$root->getChild($path);
        return $child instanceof vfsStreamFile ? $child->getContent() : $child;
    }
}
