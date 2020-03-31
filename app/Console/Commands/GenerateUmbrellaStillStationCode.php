<?php

namespace App\Console\Commands;

use App\Models\UmbrellaStation;
use Illuminate\Console\Command;

class GenerateUmbrellaStillStationCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'umbrella:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        UmbrellaStation::create(['name' => '深大科技楼', 'amount' => 0, 'status' => 1]);
        UmbrellaStation::create(['name' => '深大文科楼', 'amount' => 0, 'status' => 1]);

        $this->info('successful!');
    }
}
