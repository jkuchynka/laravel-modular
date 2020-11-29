<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ListenerMakeCommand as BaseCommand;
use Illuminate\Support\Str;
use Modular\Console\Commands\Concerns\GeneratesForModule;
use Modular\Support\Namespaces;

class ListenerMakeCommand extends BaseCommand
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
        $command = $module['key'].':'.Str::of($this->getNameInput())
            ->replace('Command', '')
            ->replaceMatches('/([A-Z])/', ' $1')
            ->slug();

        $event = $this->option('event');
        if (! Str::startsWith($event, [
            $this->getModule()->namespace(),
            'Illuminate',
            '\\'
        ])) {
            $event = $this->getModule()->namespace('events').'\\'.$event;
        }

        $replacements = array_merge($replacements, [
            'DummyEvent' => class_basename($event),
            'DummyFullEvent' => $event
        ]);
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
        $event = $this->option('event') ? $this->option('event') : 'Event';

        if (! Str::startsWith($event, [
            $this->laravel->getNamespace(),
            'Illuminate',
            '\\',
        ])) {
            $event = Namespaces::combine(
                $this->getModule()->getNamespace('events'),
                $event
            );
        }

        $stub = $this->files->get($this->getStub());

        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        $stub = str_replace(
            'DummyEvent', class_basename($event), $stub
        );

        return str_replace(
            'DummyFullEvent', trim($event, '\\'), $stub
        );
    }
}
