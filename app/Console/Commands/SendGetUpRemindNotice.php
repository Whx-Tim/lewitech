<?php

namespace App\Console\Commands;

use App\Events\TriggerGetUpNotice;
use App\Models\GetUp;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendGetUpRemindNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getup:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'remind user to sign';

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
        $todays = GetUp::whereDate('last_get_up_datetime', Carbon::today()->subDay()->toDateString())->with('user.detail')->get();
        foreach($todays as $getUp) {
            if ($getUp->user->detail->subscribe == 1) {
                event(new TriggerGetUpNotice($getUp->open_id, $getUp, 'today'));
            }
        }
    }
}
