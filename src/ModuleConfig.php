<?php

namespace Modular;

use Adbar\Dot;
use Illuminate\Support\Str;

class ModuleConfig extends Dot
{
    public function __construct($key)
    {
        parent::__construct();

        $this->init($key);
    }

    /**
     * Initialize default config settings
     *
     * @param string $key
     * @return $this
     */
    protected function init(string $key)
    {
        $name = Str::studly($key);

        $this->items = [
            'key' => $key,
            'name' => $name,
            'version' => '0.1',
            'dependsOn' => [],
            'seeds' => false,
            'seedsWeight' => 10,
            'paths' => [
                'casts' => 'Casts',
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
            'routes' => [],
            'defaultController' => Str::singular($name).'Controller'
        ];

        return $this;
    }
}
