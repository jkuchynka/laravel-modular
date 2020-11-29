<?php

namespace Modular\Console\Commands;

use Illuminate\Database\Console\Seeds\SeedCommand as BaseCommand;
use Symfony\Component\Console\Input\InputOption;

class SeedCommand extends BaseCommand
{
    protected $modularType = 'seeds';

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder', 'App\Base\Database\Seeders\DatabaseSeeder'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
        ];
    }
}
