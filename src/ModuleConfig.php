<?php

namespace Modular;

use Adbar\Dot;
use Illuminate\Support\Str;

class ModuleConfig extends Dot
{
    public function __construct($key, $modulePath)
    {
        parent::__construct();
        $this->initDefaults($key, $modulePath);
    }

    /**
     * Initialize default config settings
     * @param string $key
     * @param string $modulePath
     * @return $this
     */
    protected function initDefaults(string $key, string $modulePath)
    {
        $config = [
            'key' => $key,
            'name' => Str::studly($key),
            'version' => '0.1',
            'dependsOn' => [],
            'seeds' => false,
            'seedsWeight' => 10,
            'paths' => [
                'module' => $modulePath,
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
}
