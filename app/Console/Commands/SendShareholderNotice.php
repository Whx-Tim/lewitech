<?php

namespace App\Console\Commands;

use App\Events\TriggerShareholderNotice;
use App\Models\GetUp;
use App\Models\User;
use App\Models\WechatUserDetail;
use Illuminate\Console\Command;

class SendShareholderNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shareholder:notice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send notice to shareholder';

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

//        $wechat = app('wechat');
//        $this->info($wechat->user->get('oSiVJ0rbR8YNNF8OPzJpbl7hwjvg'));
//        $users = GetUp::all();
//        $bar = $this->output->createProgressBar(count($users));
//        foreach ($users as $user) {
////            $this->info($user->open_id);
//            event(new TriggerShareholderNotice($user->open_id, [], 'getup'));
////            $this->info($user->user->openid);
//            $bar->advance();
//        }
////


        $users = WechatUserDetail::where('subscribe', 1)->with('user')->get();
        $bar = $this->output->createProgressBar(count($users));

        foreach ($users as $user) {
            event(new TriggerShareholderNotice($user->user->openid, [], 'applyActive'));
            $bar->advance();
        }

        $bar->finish();
//        event(new TriggerShareholderNotice('oSiVJ0s1VNlyopzRrJZL4oCHbVVQ', [], 'applyActive'));
//        event(new TriggerShareholderNotice('oSiVJ0noK8UuIFGPf5Mxirgd1RmY', [], 'applyActive'));
    }
}
