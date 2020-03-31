<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Repositories\DaySign\DaySignHistoryRepository;
use App\Repositories\DaySign\DaySignRepository;
use App\Repositories\DaySign\DaySignRewardRepository;
use App\Services\RedPackService;
use Illuminate\Console\Command;

class DaySignDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daysign:daemon {name} {--id==}';

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
        $name = $this->argument('name');
        if (empty($this->option('id'))) {
            $this->{$name}();
        } else {
            $this->{$name}($this->option('id'));
        }
    }

    /**
     * 周期下架后新建一个周期
     */
    public function createTimer()
    {
        $this->downTimer();
        DaySignRepository::self()->createTimer();
    }

    /**
     * 结算完成后进行周期下架
     */
    public function downTimer()
    {
        $timer = DaySignRepository::self()->getTimer();
        if ($timer) {
            DaySignRepository::self()->downTimer($timer);
        }
    }

    /**
     * 每日早上8点半进行结算
     */
    public function settle()
    {
        DaySignHistoryRepository::self()->settle(DaySignRepository::self()->getTimer());
    }

    public function autoEveryDay()
    {
        $this->settle();
        $this->downTimer();
        $this->createTimer();
    }

    /**
     * 每日下午6点瓜分奖金
     */
    public function reward()
    {
        $shares = DaySignRewardRepository::self()->getCanShare();
        foreach ($shares as $share) {
            $pack = RedPackService::self()->day_sign_send($share->reward, $share->user);
            if ($pack->return_code == 'SUCCESS' && $pack->result_code == 'SUCCESS') {
                $share->status = DaySignRewardRepository::IS_SHARE;
                $share->save();
            }
        }

        $this->info('奖金瓜分成功');
    }
}
