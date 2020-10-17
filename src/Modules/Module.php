<?php

namespace Base\Modules;

use Adbar\Dot;
use Base\Helpers\Common;
use Illuminate\Support\Str;

class Module extends Dot
{
    /**
     * Set default module config
     *
     * @param string $key
     * @return $this
     */
    public function setDefaultConfig(string $key)
    {
        $config = [
            'key' => $key,
            'name' => Str::studly($key),
            'version' => '0.1',
            'dependsOn' => [],
            'seeds' => false,
            'seedsWeight' => 10,
            'paths' => [
                'channels' => 'Broadcasting',
                'commands' => 'Console/Commands',
                'controllers' => 'Http/Controllers',
                'events' => 'Events',
                'exceptions' => 'Exceptions',
                'factories' => 'Database/Factories',
                'jobs' => 'Jobs',
                'listeners' => 'Listeners',
                'mails' => 'Mail',
                'middleware' => 'Http/Middleware',
                'migrations' => 'Database/Migrations',
                'models' => 'Models',
                'notifications' => 'Notifications',
                'observers' => 'Observers',
                'policies' => 'Policies',
                'providers' => 'Providers',
                'queries' => 'Http/Queries',
                'requests' => 'Http/Requests',
                'resources' => 'Http/Resources',
                'rules' => 'Rules',
                'seeds' => 'Database/Seeds',
                'tests' => 'Tests',
                'views' => 'Views'
            ],
            'routesPrefix' => $key,
            'routes' => []
        ];
        $config['namespace'] = 'App\\'.$config['name'];
        $config['routesController'] = $config['name'].'Controller';
        $this->items = $config;

        return $this;
    }

    /**
     * Get the module's full namespace for a type
     *
     * @param string $type
     * @return string
     */
    public function namespace(string $type = null)
    {
        return $type ?
            Common::namespaceCombine(
                $this['namespace'],
                Common::namespaceFromPath((string) $this['paths.'.$type])
            ) :
            $this['namespace'];
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
        $base = $this->namespace();
        // Check if class is already fully namespaced
        if (preg_match('/^'.Str::of($base)->replace('\\', '\\\\').'/', $class)) {
            return $class;
        }
        return Common::namespaceCombine(
            $this->namespace($type),
            $class
        );
    }

    /**
     * Get the full module path for a type
     *
     * @param string $type
     * @return string
     */
    public function path(string $type = null)
    {
        $path = $type ?
            $this['paths.module'].'/'.$this['paths.'.$type] :
            $this['paths.module'];
        return (string) rtrim($path, '/');
    }
}
