<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand as BaseCommand;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class RequestMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * Get the replacement variables for the stub
     *
     * @param array $replacements
     * @return array
     */
    protected function getReplacements($replacements)
    {
        $module = $this->getModule();

        $replacements = array_merge($replacements, [
            '{{ namespacedModel }}' => $this->module->namespace('models') . '\\Model'
        ]);

        return $replacements;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/request.stub';
    }

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('requests');
    }
}
