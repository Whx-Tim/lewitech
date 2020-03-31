<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeRepository extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository in Repositories';

    protected function getStub()
    {
        return app_path('Library/Stubs/BlankClass.stub');
    }

    public function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }
}
