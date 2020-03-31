<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeTrait extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new trait ';

    protected function getStub()
    {
        return app_path('Library/Stubs/BlankTrait.stub');
    }

    public function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Library\Traits';
    }
}
