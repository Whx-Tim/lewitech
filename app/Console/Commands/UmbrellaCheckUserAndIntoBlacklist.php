<?php

namespace App\Console\Commands;

use App\Models\UmbrellaHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UmbrellaCheckUserAndIntoBlacklist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'umbrella:blacklist';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check the user which is bad gay of umbrella and kick user into blacklist';

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
        $date = $now->subDays(16)->toDateString();
        $this->info($date);
        $users = UmbrellaHistory::whereDate('borrow_at', $date)->where('status', 0)->with('user.umbrellaInfo')->get();
        foreach ($users as $user) {
            if ($user->user->umbrellaInfo->status != 1) {
                $user->user->blacklists()->create(['type' => 'umbrella', 'description' => '雨伞逾期未归还']);
            }
        }
    }
}
