<?php

namespace Modular;

use Adbar\Dot;
use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Modular\Concerns\LoadsRoutes;
use Modular\Exceptions\InvalidModuleException;
use Modular\Support\Namespaces;

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
        $this->config['namespace'] = Namespaces::fromClass(static::class);

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

    /**
     * Get the module config
     *
     * @return ModuleConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get the module's full namespace for a type
     *
     * @param string|null $type
     * @return string
     */
    public function getNamespace(string $type = null)
    {
        return $type ? Namespaces::combine(
            $this->get('namespace'),
            Namespaces::fromPath($this->getPath($type, true))
        ) : $this->get('namespace');
    }

    /**
     * Get a fully namespaced class
     *
     * @param string $class
     * @param string $type
     *   Path type
     * @return string
     */
    public function classFullyNamespaced(string $class, string $type)
    {
        $base = $this->getNamespace();
        // Check if class is already fully namespaced
        if (preg_match('/^'.Str::of($base)->replace('\\', '\\\\').'/', $class)) {
            return $class;
        }

        return Namespaces::combine($this->getNamespace($type), $class);
    }

    /**
     * Get the full module path for a type
     *
     * @param string|null $type
     * @param bool $relative
     * @return string
     */
    public function getPath(string $type = null, bool $relative = false)
    {
        $modulePath = $relative ? '' : $this->get('paths.module').'/';
        $path = $type ?
            $modulePath.$this->get('paths.'.$type) :
            $modulePath;

        return (string) rtrim($path, '/');
    }

    public function __get($key)
    {
        return $this->config->get($key, null);
    }
}

