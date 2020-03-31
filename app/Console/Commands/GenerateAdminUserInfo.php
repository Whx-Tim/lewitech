<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class GenerateAdminUserInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:admin {--user=} {--password=} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate admin user info';

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
        $name = $this->option('user');
        $password = $this->option('password');
        $id = $this->option('id');
//        $name = $this->ask('input your name（请输入你的用户名）');
//        $password = $this->secret('input your password（请输入你的密码）');
//        $confirmPassword = $this->secret('input your confirm password（请再输入一遍密码）');
//        $id = $this->ask('input admin id（请输入要开通的用户id）');
        User::where('id', $id)->update(['name' => $name, 'password' => bcrypt($password), 'adminset' => 5]);
        $this->info('generate successful');
        $this->info('username: ' .$name);
        $this->info('password: ' .$password);
    }
}
