<?php

namespace App\Console;

use App\Console\Commands\CheckUserSubscribe;
use App\Console\Commands\DaySignDaemon;
use App\Console\Commands\GenerateAdminUserInfo;
use App\Console\Commands\GenerateUmbrellaStillStationCode;
use App\Console\Commands\HandleDaemon;
use App\Console\Commands\MakePresenter;
use App\Console\Commands\MakeRepository;
use App\Console\Commands\MakeService;
use App\Console\Commands\MakeTrait;
use App\Console\Commands\MakeView;
use App\Console\Commands\QRcodeDaemon;
use App\Console\Commands\SendGetUpRemindNotice;
use App\Console\Commands\SendShareholderNotice;
use App\Console\Commands\SendUmbrellaLastSillNotice;
use App\Console\Commands\SendUmbrellaRemindNotice;
use App\Console\Commands\SendUmbrellaStillNotice;
use App\Console\Commands\SignDaemon;
use App\Console\Commands\UmbrellaCheckUserAndIntoBlacklist;
use App\Console\Commands\UmbrellaDaemon;
use App\Console\Commands\UpdateUserWechatInfo;
use App\Events\TriggerWarning;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CheckUserSubscribe::class,
        GenerateAdminUserInfo::class,
        UpdateUserWechatInfo::class,
        SendGetUpRemindNotice::class,
        SendShareholderNotice::class,
        HandleDaemon::class,
        MakeView::class,
        MakeService::class,
        MakeRepository::class,
        MakePresenter::class,
        MakeTrait::class,
        SendUmbrellaStillNotice::class,
        GenerateUmbrellaStillStationCode::class,
        SendUmbrellaLastSillNotice::class,
        SendUmbrellaRemindNotice::class,
        UmbrellaCheckUserAndIntoBlacklist::class,
        SignDaemon::class,
        UmbrellaDaemon::class,
        QRcodeDaemon::class,
        DaySignDaemon::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('check:subscribe')->dailyAt('1:00');
        $schedule->command('umbrella:remind')->dailyAt('8:00');
        $schedule->command('umbrella:last')->dailyAt('8:00');
//        $schedule->command('getup:remind')->dailyAt('9:30');
        $schedule->command('umbrella:blacklist')->dailyAt('18:30');
        $schedule->command('umbrella:daemon checkUserStatus')->dailyAt('00:10');

        //签到打卡---start
//        $schedule->command('sign:daemon test')->everyMinute();
        $schedule->command('sign:daemon openTimer')->monthlyOn();
        $schedule->command('sign:daemon initInfo')->monthlyOn(1, '00:30');
        $schedule->command('sign:daemon checkSign')->dailyAt('10:00');
        $schedule->command('sign:daemon everyDay')->dailyAt('9:30');
        $schedule->command('sign:daemon todayLostSign')->dailyAt('17:00');
        $schedule->command('sign:daemon changeText')->dailyAt('10:30');
        $schedule->command('sign:daemon autoWeekJob')->weeklyOn(0, '10:30');
        //签到打卡---end

        //签到打卡日结----start
        $schedule->command('daysign:daemon autoEveryDay')->dailyAt('8:30');
        $schedule->command('daysign:daemon reward')->dailyAt('18:00');
        //签到打卡日结----end

        //每周开启签到捐赠提醒
        $schedule->command('daemon:handle --name=signDonateEveryWeekAdd')->weeklyOn(1);
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }

    private function daySign(Schedule $schedule)
    {
        $schedule->command('daysi');
    }
}
