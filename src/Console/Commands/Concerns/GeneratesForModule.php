<?php

namespace Modular\Console\Commands\Concerns;

use Base\Helpers\Common;
use Illuminate\Support\Str;
use Modular\Support\Namespaces;

trait GeneratesForModule
{
    /**
     * Get the path for the built class
     *
     * @return string
     */
    abstract protected function getTargetPath();

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $path = $this->getPath($this->getNameInput());

        $name = $this->qualifyClass($path);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
             ! $this->option('force')) &&
             $this->files->exists($path)
            ) {
            $this->error($this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->sortImports($this->buildClass($name)));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Get the default replacement variables for the stub
     *
     * @param string $name
     * @return array
     */
    protected function getDefaultReplacements($name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        $namespace = $this->getNamespace($name);
        $rootNamespace = $this->rootNamespace();
        $userModel = $this->userProviderModel();

        $replacements = [
            'DummyClass' => $class,
            '{{ class }}' => $class,
            '{{class}}' => $class,

            'DummyNamespace' => $namespace,
            '{{ namespace }}' => $namespace,
            '{{namespace}}' => $namespace,

            'DummyRootNamespace' => $rootNamespace,
            '{{ rootNamespace }}' => $rootNamespace,
            '{{rootNamespace}}' => $rootNamespace,

            'NamespacedDummyUserModel' => $userModel,
            '{{ namespacedUserModel }}' => $userModel,
            '{{namespacedUserModel}}' => $userModel,

            'DummyUser' => class_basename($userModel),
            '{{ user }}' => class_basename($userModel),
        ];

        if ($this->hasOption('model')) {

            $model = $this->option('model') ? $this->option('model') : 'Model';

            $namespaceModel = $this->getModule()->namespace('models').'\\'.$model;

            $model = class_basename($namespaceModel);

            $modelVariable = lcfirst($model);

            $replacements = array_merge($replacements, [
                'NamespacedDummyModel' => $namespaceModel,
                '{{ namespacedModel }}' => $namespaceModel,
                '{{namespacedModel}}' => $namespaceModel,
                'DummyModel' => $model,
                '{{ model }}' => $model,
                '{{model}}' => $model,
                'dummyModel' => $modelVariable,
                'DummyModelVariable' => $modelVariable,
                '{{ modelVariable }}' => $modelVariable,
                '{{modelVariable}}' => $modelVariable
            ]);
        }

        return $replacements;
    }

    /**
     * Get the replacement variables for the stub
     *
     * @param array $replacements
     * @return array
     */
    protected function getReplacements($replacements)
    {
        return $replacements;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $replacements = $this->getDefaultReplacements($name);
        $replacements = $this->getReplacements($replacements);

        $stub = str_replace(
            array_keys($replacements), array_values($replacements), $stub
        );

        return $stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace('.php', '', $name);

        // Allow for handling namespaced class, module path or relative path
        $pathed = str_replace('\\', '/', $name);

        $namespacedClass = Namespaces::namespaceFromPath($pathed);

        // If fully namespaced and starts with module namespace,
        // it should use the namespaced class path
        $moduleNamespace = $this->getModule()->namespace;
        if ($name != $moduleNamespace && Str::of($namespacedClass)->contains($moduleNamespace)) {
            $relativeNamespacedClass = str_replace($moduleNamespace, '', $namespacedClass);
            return $this->getModule()->path().'/'.Namespaces::namespaceToPath($relativeNamespacedClass).'.php';
        }

        // File will go in target path
        return $this->getTargetPath().'/'.Namespaces::namespaceToPath($namespacedClass).'.php';
    }

    /**
     * Return the qualified class based on the path
     *
     * @param  string  $path
     * @return string
     */
    protected function qualifyClass($path)
    {
        $module = $this->getModule();

        // Get relative path from module root
        $relative = ltrim(str_replace($module->get('paths.module'), '', $path), '/');
        $namespace = Namespaces::namespaceFromPath($relative);

        // Combine module namespace, relative namespace and class name
        $class = pathinfo($path, PATHINFO_FILENAME);
        return Namespaces::namespaceCombine($module->namespace, $namespace).'\\'.$class;
    }
}
