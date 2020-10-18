<?php

namespace Modular;

use Illuminate\Container\Container;
use Modular\Concerns\LoadsRoutes;
use Modular\Exceptions\InvalidModuleException;

abstract class Module
{
    protected $key;

    protected $name;

    protected $description;

    protected $version = 0.1;

    protected $app;

    protected $config;

    public function __construct(Container $app)
    {
        if (!$this->key) {
            throw new InvalidModuleException('Module $key is required');
        }
        if (!$this->name) {
            throw new InvalidModuleException('Module $name is required');
        }
        $this->app = $app;
    }

    abstract protected function config() : array;

    public function boot(array $config = [])
    {
        $class = new \ReflectionClass(static::class);
        $modulePath = dirname($class->getFileName());
        $this->config = new ModuleConfig($this->key);
        $this->config['paths.module'] = $modulePath;
        $this->config['description'] = $this->description;
        $this->config['version'] = $this->version;

        // If this module has routes, add them to config, where they
        // can be overridden by the modular config
        if (in_array(
            LoadsRoutes::class,
            class_uses($this)
        )) {
            $this->config['routes'] = $this->routes();
        }

        $this->config->mergeRecursiveDistinct($this->config());
        $this->config->mergeRecursiveDistinct($config);
    }

    public function get($key, $default = null)
    {
        return $this->config->get($key, $default);
    }

    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the full module path for a type
     *
     * @param string|null $type
     * @return string
     */
    public function getPath(string $type = null)
    {
        $path = $type ?
            $this->config['paths.module'].'/'.$this->config['paths.'.$type] :
            $this->config['paths.module'];
        return (string) rtrim($path, '/');
    }

    public function __get($key)
    {
        return $this->config->get($key, null);
    }
}

