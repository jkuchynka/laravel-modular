<?php

namespace Modular\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Yaml\Yaml;

class ModuleConfigsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:configs {module? : Module key or name} {--a|array : Output as array, default is yaml}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show module configs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $modules = resolve('modules');
        $moduleKeyOrName = $this->argument('module');
        $configs = [];

        if ($moduleKeyOrName) {
            $configs[] = $modules->getModule($moduleKeyOrName);
        } else {
            $modulesConfig = $modules->getModules();
            foreach ($modulesConfig as $module) {
                $configs[] = $module;
            }
        }

        $this->line('');
        foreach ($configs as $config) {
            $array = $config->all();
            if ($this->option('array')) {
                $this->line(print_r($array, true));
            } else {
                $this->line(Yaml::dump($array));
            }
        }
    }
}
