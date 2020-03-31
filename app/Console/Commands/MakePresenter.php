<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakePresenter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:presenter {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new presenter in Presenters';

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
        $file_name = $this->argument('file');
        $file_array = explode('/', $file_name);
        $count = count($file_array);
        $path = app_path('Presenters/');
        if (!is_dir($path)) {
            mkdir($path, 0755);
        }

        for ($i = 0; $i < $file_array[$count-1]; $i++) {
            $path = $path.$file_array[$i].'/';
            if (!is_dir($path)) {
                mkdir($path, 0755);
            }
        }
        $filename = $path.$file_array[$i].'.php';
        if (!file_exists($filename)) {
            $file = fopen($filename, 'a+');
            $init = "<?php\r\nnamespace App\\Presenters;\r\n\r\nclass {$file_array[$i]}\n\r{\r\n\r\n}";
            fwrite($file, $init);
            fclose($file);

            $this->info('create a new presenter successful');
            $this->info($filename);
        } else {
            $this->info('this file is exists');
        }
    }
}
