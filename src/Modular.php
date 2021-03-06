<?php

namespace Modular;

use Adbar\Dot;
use Illuminate\Config\Repository;
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
        $modules = $this->app->make('config')->get('modular.modules', []);

        foreach ($modules as $key => $value) {
            $moduleClass = is_numeric($key) && is_string($value) ? $value : $key;
            $module = new $moduleClass($this->app);
            $config = is_array($value) ? $value : [];
            $module->boot($config);
            $this->modules[$module->key] = $module;
        }

        // Check that dependencies are installed
        foreach ($this->modules as $key => $module) {
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
     * @param $key
     * @return \Modular\Module
     * @throws ModuleNotFoundException
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

    /**
     * Get loaded modules
     *
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
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
