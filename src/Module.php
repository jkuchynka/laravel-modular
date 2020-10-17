<?php

namespace Modular;

use Illuminate\Container\Container;
use Illuminate\Support\Str;
use Modular\Concerns\LoadsRoutes;
use Modular\Exceptions\InvalidModuleException;
use Symfony\Component\Yaml\Yaml;

abstract class Module
{
    use LoadsRoutes;

    protected $key;

    protected $app;

    protected $config;

    public function __construct(Container $app)
    {
        if (!$this->key) {
            throw new InvalidModuleException('Module $key is required');
        }
        $this->app = $app;
    }

    public function boot(array $config = [])
    {
        $class = new \ReflectionClass(static::class);
        $modulePath = dirname($class->getFileName());
        $filename = $modulePath.'/'.$this->key.'.config.yaml';
        $parser = new Yaml;
        $moduleConfig = $parser->parseFile($filename);
        $this->config = new ModuleConfig($this->key, $modulePath);
        $this->config->mergeRecursiveDistinct($moduleConfig);
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

