<?php

namespace App\Console\Commands;

use App\Events\TriggerUmbrellaNotice;
use App\Models\UmbrellaHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendUmbrellaRemindNotice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'umbrella:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send umbrella remind notice to user';

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
        $now = Carbon::now();
        $date = $now->subDays(12)->toDateString();
        $this->info($date);
        $users = UmbrellaHistory::whereDate('borrow_at', $date)->where('status', 0)->with('user')->get();
        foreach ($users as $user) {
            if ($user->user->umbrellaInfo->status != 1) {
                event(new TriggerUmbrellaNotice($user->user->openid, ['user' => $user->user], 'remindStill'));
            }
        }
    }
}
