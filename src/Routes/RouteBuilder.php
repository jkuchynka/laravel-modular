<?php

namespace Modular\Routes;

use Illuminate\Support\Str;
use Modular\Exceptions\InvalidRouteException;
use Modular\Module;
use Modular\ModuleConfig;

/**
 * Class RouteBuilder
 * Builds a route from a config that can be plugged into Laravel routes
 * e.g., with this config:
 * [
 *  'key' => 'users',
 *  'name' => 'Users',
 *  'defaultController' => 'UserController',
 *  'routePrefix' => 'users',
 * ]
 *
 * Creates apiResource with names users.index, users.show, etc...
 * mapped to UserController
 * ['uri' => '', 'method' => 'api-resource']
 *
 * Creates GET /users/profile NAME users.profile ACTION UserController@profile
 * ['uri' => 'profile']
 *
 * Creates POST /users/{user}/avatar NAME users.storeAvatar ACTION ProfileController@store
 * ['uri' => '{user}/avatar', 'name' => 'storeAvatar', 'uses' => 'ProfileController@store']
 * @package Modular\Routes
 */
class RouteBuilder
{
    protected $route;

    protected $builtRoutes;

    protected $module;

    protected $method;

    protected $name;

    protected $uri;

    protected $uses;

    public function __construct(Module $module, array $route)
    {
        $this->module = $module;
        $this->route = $route;
    }

    public function getRoutes()
    {
        if (!$this->builtRoutes) {
            $this->buildRoutes();
        }
        return $this->builtRoutes;
    }

    protected function buildRoutes()
    {
        $this->validate();
        $this->builtRoutes = [
            'uri' => $this->getUri(),
            'method' => $this->getMethod(),
            'name' => $this->getName(),
            'uses' => $this->getUses()
        ];
    }

    public function getUri()
    {
        if (!$this->uri) {
            // URI is required
            if (!isset($this->route['uri'])) {
                throw new InvalidRouteException(
                    'Route config is invalid. Missing uri. ' . print_r($this->route, 1)
                );
            }

            // Route URI should be prefixed by module key
            // or routesPrefix setting, only if relative URI
            $this->uri = $this->route['uri'];
            if (!$this->uri || $this->uri[0] !== '/') {
                $prefix = $this->module->routesPrefix;
                $this->uri = $this->uri ? $prefix.'/'.$this->uri : $prefix;
            }

            // Convert :param syntax to {param}
            $this->uri = preg_replace('/:([^\/]*)/', '{$1}', $this->uri);
        }
        return $this->uri;
    }

    public function isResource()
    {
        return in_array($this->getMethod(), ['resource', 'api-resource']);
    }

    public function getMethod()
    {
        if (!$this->method) {
            // Method defaults to get
            $this->method = isset($this->route['method']) ? strtolower($this->route['method']) : 'get';

            if (!in_array($this->method, [
                'resource', 'api-resource', 'get', 'post', 'put', 'delete'
            ])) {
                throw new InvalidRouteException(
                    'Route config is invalid. Invalid method. ' . print_r($this->route, 1)
                );
            }
        }
        return $this->method;
    }

    public function getName()
    {
        if (!$this->name) {
            $names = [$this->module->key];
            if (!empty($this->route['name'])) {
                $names[] = $this->route['name'];
            } else {
                $tempUri = explode('/', $this->route['uri']);
                while ($temp = array_shift($tempUri)) {
                    // Skip params
                    if (!Str::contains($temp, '{')) {
                        $names[] = Str::camel($temp);
                    }
                }
            }
            $this->name = implode('.', $names);
        }
        return $this->name;
    }

    public function getUses()
    {
        if (!$this->uses) {
            // Defaults to module's defaultController
            $this->uses = empty($this->route['uses']) ?
                $this->module->defaultController :
                $this->route['uses'];

            // Check if uses specifies controller
            if (!Str::contains($this->uses, 'Controller')) {
                $this->uses = $this->module->defaultController.'@'.$this->uses;
            }

            // Check if uses specifies function
            if (!$this->isResource() && !Str::contains($this->uses, '@')) {
                $this->uses .= '@';
                if (empty($this->route['uri'])) {
                    $this->uses .= $this->module->key;
                } else {
                    $function = Str::of($this->route['uri'])
                        ->replace('/', '_')
                        ->camel();
                    $this->uses .= $function;
                }
            }

            if ($this->isResource() && Str::contains($this->uses, '@')) {
                throw new InvalidRouteException(
                    'Route config is invalid. Resource route should point to controller. ' . print_r($this->route, 1)
                );
            }
        }
        return $this->uses;
    }

    /**
     * Validate that we can build this route
     * @throws InvalidRouteException
     */
    public function validate()
    {
        if (!isset($this->route['uri'])) {
            throw new InvalidRouteException(
                'Route config is invalid. Missing uri. ' . print_r($this->route, 1)
            );
        }

        if (isset($this->route['method']))

        if (
            isset($route['method']) &&
            isset($route['uses']) &&
            $route['method'] == 'resource' &&
            Str::contains($route['uses'], '@')
        ) {
            throw new InvalidRouteException(
                'Route config is invalid. Resource route should point to controller. ' . print_r($route, 1)
            );
        }
    }
}
