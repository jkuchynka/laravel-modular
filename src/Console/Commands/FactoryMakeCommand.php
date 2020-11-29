<?php

namespace Modular\Console\Commands;

use Illuminate\Database\Console\Factories\FactoryMakeCommand as BaseCommand;
use Illuminate\Support\Str;
use Modular\Console\Commands\Concerns\GeneratesForModule;
use Modular\Support\Namespaces;

class FactoryMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/factory.stub';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $model = $this->option('model')
            ? $this->option('model')
            : $this->guessModelName($name);

        $namespaceModel = Namespaces::combine(
            $this->getModule()->getNamespace('models'),
            $model
        );

        $namespace = $this->getModule()->getNamespace('factories');

        $replace = [
            'NamespacedDummyModel' => $namespaceModel,
            '{{ namespacedModel }}' => $namespaceModel,
            '{{namespacedModel}}' => $namespaceModel,
            'DummyModel' => $model,
            '{{ model }}' => $model,
            '{{model}}' => $model,
        ];

        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        return str_replace(
            array_keys($replace), array_values($replace), $stub
        );
    }

    /**
     * Guess the model name from the Factory name.
     *
     * @param  string  $name
     * @return string
     */
    protected function guessModelName($name)
    {
        if (Str::endsWith($name, 'Factory')) {
            $name = substr($name, 0, -7);
        }

        return class_basename($name);
    }
}
