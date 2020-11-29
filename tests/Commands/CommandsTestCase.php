<?php

namespace Modular\Tests\Commands;

use Modular\Modular;
use Modular\Providers\ConsoleServiceProvider;
use Modular\Tests\BaseTestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;
use Illuminate\Config\Repository;

// Autoloads classes in VFS namespace from vfsStream filesystem
spl_autoload_register(function ($class) {
    if (preg_match('/^VFS\\\(.*)$/', $class, $matches)) {
        @include('vfs://root/'.str_replace('\\', '/', $matches[1]).'.php');
    }
});

abstract class CommandsTestCase extends BaseTestCase
{
    protected $loadModules = ['base', 'foo_bar'];

    protected static $root;

    static public function setUpBeforeClass(): void
    {
        static::$root = vfsStream::setup();
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Clear the VFS filesystem
        if (static::$root->hasChild('FooBar')) {
            static::$root->removeChild('FooBar');
        }

        mkdir(vfsStream::url('root/FooBar'));
        file_put_contents(vfsStream::url('root/FooBar/FooBar.php'), <<<EOL
<?php

namespace VFS\FooBar;

use Modular\Module;

class FooBar extends Module
{
    protected \$key = 'foo_bar';

    protected \$name = 'FooBar';

    protected function config(): array
    {
        return [];
    }
}
EOL
);

        // Boot VFS module
        $this->app->singleton('modular', function ($app) {
            $config = $app->make(Repository::class);
            $config->set('modular', [
                'modules' => [
                    'VFS\FooBar\FooBar',
                ],
            ]);

            $modular = new Modular($app);
            $modular->bootModules();
            return $modular;
        });

        $this->app->register(ConsoleServiceProvider::class);
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
