<?php

namespace Modular\Concerns;

use Illuminate\Support\Facades\Route;
use Modular\Exceptions\InvalidModuleException;
use Modular\Routes\RouteBuilder;

trait LoadsRoutes
{
    abstract protected function routes(): array;

    /**
     * Load routes
     * @throws InvalidModuleException
     */
    public function loadRoutes()
    {
        // First level of routes should be array of groups
        foreach ($this->get('routes', []) as $group) {
            if (!isset($group['routes'])) {
                throw new InvalidModuleException('Invalid module config: '.$this->key.' route group missing routes');
            }

            Route::group([
                'prefix' => isset($group['prefix']) ? $group['prefix'] : '',
                'middleware' => isset($group['middleware']) ? $group['middleware'] : 'web'
            ], function () use ($group) {
                foreach ($group['routes'] as $moduleRoute) {
                    $builder = new RouteBuilder($this, $moduleRoute);

                    if ($builder->isResource()) {
                        $name = $builder->getName();
                        if ($builder->getMethod() === 'api-resource') {
                            Route::apiResource($builder->getUri(), $builder->getUses(), [
                                'as' => $name
                            ])->names([
                                'index' => $name . '.index',
                                'store' => $name . '.store',
                                'show' => $name . '.show',
                                'update' => $name . '.update',
                                'destroy' => $name . '.destroy'
                            ]);
                        } else {
                            Route::resource($builder->getUri(), $builder->getUses(), [
                                'as' => $name
                            ])->names([
                                'index' => $name . '.index',
                                'create' => $name . '.create',
                                'store' => $name . '.store',
                                'show' => $name . '.show',
                                'edit' => $name . '.edit',
                                'update' => $name . '.update',
                                'destroy' => $name . '.destroy'
                            ]);
                        }
                    } else {
                        $method = $builder->getMethod();
                        Route::$method($builder->getUri(), [
                            'as' => $builder->getName(),
                            'uses' => $builder->getUses()
                        ]);
                    }
                }
            });
        }
    }
}
