<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ListenerMakeCommand as BaseCommand;
use Illuminate\Support\Str;
use Modular\Console\Commands\Concerns\GeneratesForModule;

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
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('listeners');
    }
}
