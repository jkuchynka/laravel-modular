<?php

namespace Modular\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand as BaseCommand;
use Illuminate\Support\Facades\File;
use Modular\Console\Commands\Concerns\GeneratesForModule;

class MigrateMakeCommand extends BaseCommand
{
    use GeneratesForModule;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:migration {module : The name of the module} {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (! is_null($targetPath = $this->input->getOption('path'))) {
            $path = ! $this->usingRealPath()
                            ? $this->getTargetPath().'/'.$targetPath
                            : $targetPath;
        } else {
            $path = $this->getTargetPath();
        }

        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('migrations');
    }
}
