<?php

namespace Modular\Console\Commands;

use Illuminate\Foundation\Console\MailMakeCommand as BaseCommand;

class MailMakeCommand extends BaseCommand
{
    use Concerns\HasModuleArgument;
    use Concerns\GeneratesForModule {
        handle as generatesHandle;
    }

    /**
     * Get the replacement variables for the stub
     *
     * @param array $replacements
     * @return array
     */
    protected function getReplacements($replacements)
    {
        if ($this->option('markdown')) {
            $replacements['DummyView'] = $this->option('markdown');
        }
        return $replacements;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->generatesHandle() === false && ! $this->option('force')) {
            return;
        }

        if ($this->option('markdown')) {
            $this->writeMarkdownTemplate();
        }
    }

    /**
     * Get the path for the built class
     *
     * @return string
     */
    protected function getTargetPath()
    {
        return $this->getModule()->path('mails');
    }

    /**
     * Write the Markdown template for the mailable.
     *
     * @return void
     */
    protected function writeMarkdownTemplate()
    {
        $path = $this->getModule()->path('views').'/'.str_replace('.', '/', $this->option('markdown')).'.blade.php';

        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true);
        }

        $this->files->put($path, file_get_contents(__DIR__.'/stubs/markdown.stub'));
    }
}
