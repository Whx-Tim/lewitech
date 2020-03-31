<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:view {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new blade.php file in views';

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
        $file_path = $this->argument('file');
        $file_array = explode('.', $file_path);
        $array_count = count($file_array);
        $path = base_path('resources/views').'/';

        for ($i = 0; $i < $array_count-1 ; $i ++) {
            $path = $path.$file_array[$i].'/';
            if (!is_dir($path)) {
                mkdir($path, 0755);
            }
        }
        $filename = $path.$file_array[$i].'.blade.php';

        if (!file_exists($filename)) {
            $file = fopen($filename, 'a+');
            fclose($file);
        }

        $this->info($filename);
        $this->info('create a new view successful');


//        if (is_dir(''))
//        $this->info($path);
//        $file = fopen()
    }
}
