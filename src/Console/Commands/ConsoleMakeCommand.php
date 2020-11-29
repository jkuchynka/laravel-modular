<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ConsoleMakeCommand as BaseCommand;
use Illuminate\Support\Str;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class ConsoleMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    protected $modularType = 'command';

    /**
     * Get the replacement variables for the stub
     *
     * @param array $replacements
     * @return array
     */
    protected function getReplacements($replacements)
    {
        $module = $this->getModule();
        $command = $module['key'].':'.Str::of($this->getNameInput())
            ->replace('Command', '')
            ->replaceMatches('/([A-Z])/', ' $1')
            ->slug();
        $replacements = array_merge($replacements, [
            '{{ command }}' => $command
        ]);
        return $replacements;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);
        $command = $this->getModule()->key . ':';
        $command .= Str::of($this->getNameInput())
            ->replaceMatches('/([A-Z])/', ' $1')
            ->slug();

        return str_replace('command:name', $command, $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/console.stub';
    }
}
