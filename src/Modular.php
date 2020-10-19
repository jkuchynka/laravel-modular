<?php

namespace Modular;

use Adbar\Dot;
use Modular\Module;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Modular\Exceptions\ModuleNotFoundException;

class Modular
{
    protected $app;

    protected $modules = [];

    protected $loaded = false;

    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    public function bootModules()
    {
        $modules = $this->app['config']->get('modular.modules', []);

        foreach ($modules as $moduleClass => $config) {
            $module = new $moduleClass($this->app);
            $module->boot($config);
            $this->modules[$module->key] = $module;
        }

        // Check that dependencies are installed
        foreach ($modules as $key => $module) {
            foreach ($module->dependsOn as $dependsKey) {
                if (!isset($this->modules[$dependsKey])) {
                    throw new ModuleNotFoundException(
                        'Module: '.$dependsKey.' not installed. Is a dependency of: '.$key
                    );
                }
            }
        }
    }

    /**
     * Get a loaded module by key
     *
     * @param  string $key
     * @return Module
     */
    public function getModule($key): Module
    {
        if (isset($this->modules[$key])) {
            return $this->modules[$key];
        }
        foreach ($this->modules as $module) {
            if ($module->name == $key) {
                return $module;
            }
        }
        throw new ModuleNotFoundException('Module: '.$key.' not installed.');
    }

    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get the modules path
     *
     * @return string
     */
    public function getModulesPath(): string
    {
        $path = $this->app['config']->get('modular.paths.modules', 'app');
        if ($path[0] === '/' || Str::contains($path, '://')) {
            return $path;
        }
        return base_path().'/'.$path;
    }

    /**
     * Get the combined version of all modules.
     * @todo:
     *      The theory here is that updating the version of a module,
     *      or adding a module, will change the app-level version and could be used
     *      for invalidating caches, re-fetching schemas, etc...
     *
     * @return float
     */
    public function version()
    {
        $version = 0.0;
        foreach ($this->modules as $module) {
            $version += (float) $module['version'];
        }
        return (string) $version;
    }
}
