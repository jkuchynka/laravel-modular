<?php

namespace Modular\Console\Commands;

use Base\Helpers\Common;
use Illuminate\Routing\Console\ControllerMakeCommand as BaseCommand;
use InvalidArgumentException;
use Modular\Console\Commands\Concerns\GeneratesForModule;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('controllers');
    }

    /**
     * Get the replacement variables for the stub
     *
     * @param array $replacements
     * @return array
     */
    protected function getReplacements($replacements)
    {
        $module = $this->getModule();

        $controllerNamespace = $module['namespace'] . '\\' . rtrim(str_replace('/', '\\', $module['paths.controllers']), '\\');

        $replacements = array_merge($replacements, [
            '{{ controllerNamespace }}' => $controllerNamespace,
            "use {$controllerNamespace}\Controller;\n" => '',
            '{{ namespacedModel }}' => $this->module->namespace('models') . '\\Model'
        ]);

        if ($this->option('parent')) {
            $replacements = array_merge($replacements, $this->buildParentReplacements());
        }

        if ($this->option('model')) {
            $replacements = $this->buildModelReplacements($replacements);
        }

        return $replacements;
    }

    /**
     * Build the replacements for a parent controller.
     *
     * @return array
     */
    protected function buildParentReplacements()
    {
        $parentModelClass = $this->parseModel($this->option('parent'));

        if (! class_exists($parentModelClass)) {
            if ($this->confirm("A {$parentModelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['module' => $this->getModule()['key'], 'name' => $parentModelClass]);
            }
        }

        return [
            'ParentDummyFullModelClass' => $parentModelClass,
            '{{ namespacedParentModel }}' => $parentModelClass,
            '{{namespacedParentModel}}' => $parentModelClass,
            'ParentDummyModelClass' => class_basename($parentModelClass),
            '{{ parentModel }}' => class_basename($parentModelClass),
            '{{parentModel}}' => class_basename($parentModelClass),
            'ParentDummyModelVariable' => lcfirst(class_basename($parentModelClass)),
            '{{ parentModelVariable }}' => lcfirst(class_basename($parentModelClass)),
            '{{parentModelVariable}}' => lcfirst(class_basename($parentModelClass)),
        ];
    }

    /**
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['module' => $this->getModule()['key'], 'name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        $path = $this->getModule()->path('models').'/'.Common::namespaceToPath($model).'.php';
        $class = $this->qualifyClass($path);
        // print_r([$path, $class]);
        // echo 'path: '.file_exists('vfs://root/app/FooBar/Models/FooBar.php');
        // die;
        return $class;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        // Model and resource controllers both correspond to
        // a full resourceful controller with query builder setup
        // The api option is redundant here, so it will use
        // the same stub as the resource controller

        $stub = null;

        if ($this->option('parent')) {
            $stub = 'controller.nested.stub';
        } elseif ($this->option('model')) {
            $stub = 'controller.model.stub';
        } elseif ($this->option('invokable')) {
            $stub = 'controller.invokable.stub';
        } elseif ($this->option('resource')) {
            $stub = 'controller.stub';
        }

        if ($this->option('api') && is_null($stub)) {
            $stub = 'controller.api.stub';
        } elseif ($this->option('api') && ! is_null($stub) && ! $this->option('invokable')) {
            $stub = str_replace('.stub', '.api.stub', $stub);
        }

        if ($this->option('api') && $this->option('querybuilder')) {
            $stub = str_replace('.stub', '.querybuilder.stub', $stub);
        }

        $stub = $stub ?? 'controller.plain.stub';

        return __DIR__.'/stubs/controller/'.$stub;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        $options = parent::getOptions();
        $options[] = ['querybuilder', 'b', InputOption::VALUE_NONE, 'Generate an api controller with query builder methods'];
        return $options;
    }
}
