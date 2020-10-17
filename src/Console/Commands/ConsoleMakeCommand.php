<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\ConsoleMakeCommand as BaseCommand;
use Illuminate\Support\Str;

class ConsoleMakeCommand extends BaseCommand
{
    use Concerns\HasModuleArgument,
        Concerns\GeneratesForModule;

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
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('commands');
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
