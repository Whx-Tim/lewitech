<?php

namespace App\Console\Commands;

use App\Events\TriggerUmbrellaNotice;
use App\Models\UmbrellaHistory;
use App\Models\User;
use Illuminate\Console\Command;

class SendUmbrellaStillNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'umbrella:still';

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

//        $histories = UmbrellaHistory::whereDate('borrow_at', '2017-09-05')->where('status', 0)->with('user')->get();
//        foreach ($histories as $history) {
//            if ($history->user->umbrellaInfo->status != 1) {
//                $this->info($history->user->real_name);
//                event(new TriggerUmbrellaNotice($history->user->openid, ['user' => $history->user], 'remindStill'));
//            }
//        }
//        $user = User::where('id', 533)->first();

        $user = User::where('id', 154)->first();
        event(new TriggerUmbrellaNotice($user->openid, ['user' => $user], 'lastStill'));
        event(new TriggerUmbrellaNotice($user->openid, ['user' => $user], 'remindStill'));
        event(new TriggerUmbrellaNotice($user->openid, ['user' => $user], 'relieveNotice'));
        event(new TriggerUmbrellaNotice($user->openid, ['user' => $user], 'stillNew'));
    }
}
