<?php

namespace App\Console\Commands;

use App\Events\SendWeatherReportToUser;
use App\Events\TriggerSignEvent;
use App\Events\TriggerWarning;
use App\Instance\SelfIoc;
use App\Models\Card;
use App\Models\Config;
use App\Models\Sign;
use App\Models\SignCard;
use App\Models\SignDeal;
use App\Models\SignInfo;
use App\Models\SignMedalWeek;
use App\Models\SignTimer;
use App\Models\SignTimerApply;
use App\Models\Temp;
use App\Models\User;
use App\Repositories\CardRepository;
use App\Repositories\SignCardRepository;
use App\Repositories\SignDealRepository;
use App\Repositories\SignInfoRepository;
use App\Repositories\SignMedalRepository;
use App\Repositories\SignRepository;
use App\Repositories\SignTimerRepository;
use App\Services\RedPackService;
use App\Services\SignService;
use Carbon\Carbon;
use EasyWeChat\Message\Text;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SignDaemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sign:daemon {name} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'handle all daemon of sign';

    private $sign_service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->sign_service = new SignService();
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

    public function test()
    {
        $this->info('test success');
//        $user = User::find(154);
//        $sign = Sign::where('user_id', 154)->first();
//        event(new SendWeatherReportToUser($user, $sign));
//        $user = User::find(49);
//        event(new TriggerSignEvent($user, 'today'));
//        $user = User::find(154);
//        event(new TriggerSignEvent($user, 'today'));
//        $this->info('all_apply test2');
    }

    /**
     * 每日10点检测今日没有签到的用户，并将时间置为10点并计算早起值，同时今日签到状态为失败
     */
    public function checkSign()
    {
        $users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', (SignTimerRepository::static_getOpeningTimer())->id);
        })->orderBy('created_at', 'desc')->with('detail')->get();
        $sign_users = User::whereHas('signs', function ($query) {
            $query->whereDate('today_time', Carbon::now()->toDateString());
        })->orderBy('created_at', 'desc')->with('detail')->get();
        $this->info(count($users));
        foreach ($users as $user_key => $user) {
            foreach ($sign_users as $key => $sign_user) {
                if ($user->id == $sign_user->id) {
                    unset($users[$user_key]);
                    unset($sign_users[$key]);
                    break;
                }
            }
        }
        $this->info(count($users));
        $sign_timer_repository = new SignTimerRepository(new SignTimer());
        $sign_info_repository = new SignInfoRepository(new SignInfo());

        $timer = $sign_timer_repository->getOpeningTimer();
        foreach ($users as $user) {
            $sign_info = $sign_info_repository->lostSign($user);
            $sign = $user->signs()->create([
                'today_time' => Carbon::today()->addHours(10)->toDateTimeString(),
                'today_status' => SignRepository::LOST_SIGN,
                'sign_timer_id' => $timer->id,
                'time_value' => Carbon::today()->addHours(10)->diffInSeconds(Carbon::today()->addHours(5))
            ]);

            (new SignRepository($sign))->computeTimeValue(null, $user);
        }
        event(new TriggerWarning('今日检测没有签到的用户完成'));
        $sign_service = new SignService();
        foreach ($users as $user) {
            $sign_service->saveSharePoster($user);
        }
        event(new TriggerWarning('今日没有签到的用户生成分享海报完成'));
    }

    /**
     * 在每日9点30分时，提醒正常打卡用户打卡
     */
    public function everyDay()
    {
        $users = User::whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN);
        })->whereHas('signApplies', function ($query) {
            $query->where('timer_id', (SignTimerRepository::static_getOpeningTimer())->id);
        })->orderBy('created_at', 'desc')->with('detail')->get();
        $sign_users = User::whereHas('signs', function ($query) {
            $query->whereDate('today_time', Carbon::today()->toDateString());
        })->orderBy('created_at', 'desc')->get();
//        $this->info(count($users));
        foreach ($users as $user_key => $user) {
            foreach ($sign_users as $key => $sign_user) {
                if ($user->id == $sign_user->id) {
                    unset($users[$user_key]);
                    unset($sign_users[$key]);
                    break;
                }
            }
        }
        $this->info(count($users));

//        $user = User::find(154);
//        event(new TriggerSignEvent($user, 'today'));
        foreach ($users as $user) {
            if ($user->detail->subscribe == 1) {
                $this->info($user->id);
                event(new TriggerSignEvent($user, 'today'));
            }
        }
        event(new TriggerWarning(count($users).'人进行了每日9点半提醒'));
    }

    /**
     * 提醒报名了的漏签用户，漏签第一次以及漏签第二次的用户，在每日17点发送
     */
    public function todayLostSign()
    {
        $users = User::whereHas('sign_info', function ($query) {
            $query->where('is_apply', 1)->whereIn('status', [SignInfoRepository::FIRST_LOST_SIGN, SignInfoRepository::SECOND_LOST_SIGN]);
        })->with('sign_info')->with('detail')->get();
//        $user = User::find(154);
//        event(new TriggerSignEvent($user, 'lostSign'));
        foreach ($users as $user) {
            if ($user->detail->subscribe == 1) {
                event(new TriggerSignEvent($user, 'lostSign'));
            }
        }
        event(new TriggerWarning('今日漏签用户补签通知完成'));
    }


    /**
     * 自动化每周周报生成发送
     */
    public function autoWeekJob()
    {
        $this->week();
        $this->sendWeekPoster();
        event(new TriggerWarning('自动化每周打卡周报发送成功'));
    }

    /**
     * 提醒每周有签到的用户生成海报
     */
    public function week()
    {
        $sign_service = new SignService();
        $sign_service->medalEveryWeek();
    }

    public function sendWeekPoster()
    {
        $sign_weeks = SignMedalWeek::whereDate('created_at', Carbon::today()->toDateString())->with('user')->get();
        $sign_service = new SignService();
        $bar = $this->output->createProgressBar(count($sign_weeks));

        foreach ($sign_weeks as $sign) {
//            $this->info($sign->user->id);
            $sign_service->sendImageWithDelete(public_path('images/sign/week/'.$sign->user->id.'.jpeg'), $sign->user);
            $bar->advance();
        }

        $bar->finish();
    }

    /**
     * 创建下一轮签到报名 在每月25号开始创建
     */
    public function createApplyTimer()
    {
//        SelfIoc::make('SignTimerRepository', 'create');
        $sign_timer_repository = new SignTimerRepository(new SignTimer());
        $sign_timer_repository->create();
    }

    /**
     * 开启正在报名的签到周期 在周期开启时间规定下，进行开启
     */
    public function openTimer()
    {
        $sign_timer_repository = new SignTimerRepository(new SignTimer());
        $this->closeTimer();
        $sign_timer = $sign_timer_repository->openTimer();
        event(new TriggerWarning('签到打卡周期开启成功'));
    }

    /**
     * 关闭本轮周期
     */
    public function closeTimer()
    {
        $sign_timer_repository = new SignTimerRepository(new SignTimer());
        $sign_timer_repository->closeTimer();
        event(new TriggerWarning('签到打卡关闭成功'));
    }

    public function refund()
    {
        $sign_deal_respository = new SignDealRepository(new SignDeal());
        $sign_timer_repository = new SignTimerRepository(new SignTimer());
//        $user = User::find(13947);
//        $sign_deal = $user->signDeals()->where('result_code', SignDealRepository::CONTINUE_SUCCESS_STATUS)->first();
        $sign_deal = SignDeal::where('id', 957)->first();
//        $sign_deal = $sign_deal_respository->getUserSuccessDeal($user);
////        $timer = $sign_timer_repository->getApplyingTimer();
//        $timer = $sign_timer_repository->getOpeningTimer();
//        $timer = SignTimer::where('id', 3)->first();
//        $sign_deal_respository->refund($sign_deal, $timer);
        $sign_deal_respository->refund($sign_deal);
//        $staff = app('wechat')->staff;
//        $text = new Text();
//        $text->content = '您好，系统检测到您报名金额异常，现在将您多缴纳的押金退回，请注意查收微信退款的相关信息。';
//        $staff->message($text)->to($user->openid)->send();
        $this->info('success');
    }

    public function changeText()
    {
        $text = (Config::where('type', 'signText')->first())->content;
        $text = json_decode($text);
        unset($text[0]);
        $text = array_values($text);
        Config::where('type', 'signText')->update([
            'content' => json_encode($text)
        ]);
    }

    public function insertText()
    {
        $text = file_get_contents(base_path('sign_week_text'));
        Config::where('type', 'signText')->update([
            'content' => $text
        ]);
    }

    public function applySuccess()
    {
        $user = User::find(154);
        event(new TriggerSignEvent($user, 'applySuccess'));
        $user = User::find(84);
        event(new TriggerSignEvent($user, 'applySuccess'));
    }

    public function createCard()
    {
        Card::create([
            'name' => '12月九折卡',
            'description' => '12月早起打卡可以进行九折报名',
            'status' => 1,
            'regulation' => 0.9,
            'regulation_type' => CardRepository::CONFINE_TYPE,
            'start_at' => Carbon::today()->startOfMonth()->addDays(25)->toDateTimeString(),
            'end_at' => Carbon::today()->startOfMonth()->addDays(29)->toDateTimeString()
        ]);
    }

    public function distributeCard()
    {
//        $users = User::whereHas('sign_info', function ($query) {
//            $query->where('status', SignInfoRepository::NORMAL_SIGN)
//                ->where('is_apply', 1)
//                ->where('is_free', 0);
//        })->get();
        $sign_card_repository = new SignCardRepository(new SignCard());
        $card = Card::where('name', '九折卡')->first();
//        foreach ($users as $user) {
//            $sign_card_repository->attainCard($card, $user);
//        }
        $user = User::find(154);
        $sign_card_repository->attainCard($card, $user);
    }



    public function sendApplyMessage()
    {
        $apply_users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', 3);
        })->get();
        $all_users = User::whereHas('sign_info', function ($query) {
            $query->where('is_apply', 1);
        })->get();
        $this->info(count($apply_users));
        $this->info(count($all_users));
        foreach ($all_users as $all_key => $user) {
            foreach ($apply_users as $key => $apply_user) {
                if ($apply_user->id == $user->id) {
                    unset($apply_users[$key]);
                    unset($all_users[$all_key]);
                }
            }
            if (in_array($user->id, json_decode((Temp::where('type', 'signApplyRefuse')->first())->data))) {
                $this->info($user->id);
                unset($all_users[$all_key]);
            }
        }
        $this->info(count($all_users));
//        $user = User::find(154);
//        $this->applyMessage($user->id);
        foreach ($all_users as $all_user) {
            $this->info($all_user->openid);
            try {
                $this->applyMessage($all_user->id);
            } catch (\Exception $exception) {
                Log::warning($exception);
                event(new TriggerSignEvent($all_user, 'applyRemind'));
            }
            $this->info($all_user->openid.'成功');
        }

    }

    /**
     * 每月将失败的付费用户状态变更为close
     */
    public function monthEndCloseUser()
    {
        $sign_deal_repository = new SignDealRepository(new SignDeal());
        $sign_deal_repository->failUserClose();

        $this->info('successful');
    }

    /**
     * 给失败的用户发送失败的模板消息
     */
    public function failUser()
    {
        $users = $this->sign_service->getFailUser();
        $this->info(count($users));
        foreach ($users as $user) {
            $this->info($user->id);
            $deals = $user->signDeals;
            foreach ($deals as $deal) {
                $this->info($deal->sign_timer_id);
                $this->info($deal->result_code);
            }
            event(new TriggerSignEvent($user, 'failRemind'));
        }
    }

    /**
     * 付费用户的状态为第一次补签成功或是第二次补签成功的用户进行退款与模板消息的通知
     */
    public function notFailUser()
    {
        $users = $this->sign_service->getNoFailUser();
        $this->info(count($users));
        $sign_deal = new SignDealRepository(new SignDeal());
        foreach ($users as $user) {
            $this->info($user->id);
            $deals = $user->signDeals;
            foreach ($deals as $deal) {
                $this->info($deal->sign_timer_id);
                $this->info($deal->result_code);
                if ($deal->result_code == SignDealRepository::OVER_STATUS) {
                    $this->overStatusRefund($deal);
                } else {
                    $sign_deal->refund($deal);
                }
            }
//            event(new TriggerSignEvent($user, 'notFailRemind'));
        }
    }

    /**
     * 成功但是没有报名下一轮的用户进行退款
     */
    public function successButNotApply()
    {
        $users = $this->sign_service->getSuccessButNotApplyUser();
        $this->info(count($users));
        $sign_deal = new SignDealRepository(new SignDeal());
        foreach ($users as $user) {
            $this->info('用户id:'.$user->id);
            $deals = $user->signDeals;
            foreach ($deals as $deal) {
                $this->info('周期id:'.$deal->sign_timer_id);
                $this->info($deal->result_code);
                $this->info($deal->out_trade_no);
                if ($deal->result_code == SignDealRepository::OVER_STATUS) {
                    $this->overStatusRefund($deal);
                } else {
                    $sign_deal->refund($deal);
                }
            }
        }
    }

    /**
     * 溢出状态的用户进行退款
     *
     * @param SignDeal $signDeal
     * @date 2018-03-27
     * @author timx
     */
    public function overStatusRefund(SignDeal $signDeal)
    {
        $user = $signDeal->user;
        $red_pack_service = new RedPackService();
        $money = $signDeal->cash_fee / 100;
        $signDeal->result_code = SignDealRepository::OVER_BACK_STATUS;
        try {
            $red_pack_service->sign_refund_send($money, $user);
            $signDeal->save();
        } catch (\Exception $e) {
            $this->info('some error display');
        }
    }

    /**
     * 每月总结数据到数据库
     *
     * @date 2018-03-27
     */
    public function everyMonthSummary()
    {
        $this->sign_service->summaryMonth();
    }

    /**
     * 初始化新月的打卡数据
     * 自动在每月头一天的00:30执行
     */
    public function initInfo()
    {
        $users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', (SignTimerRepository::static_getOpeningTimer())->id);
//            $query->where('timer_id', (SignTimerRepository::self()->getApplyingTimer())->id);
        })->get();
        $id_arr = [];
        foreach ($users as $user) {
            $id_arr []= $user->id;
        }
        SignInfo::where('id', '>', 0)->update([
            'month_count' => 0,
            'duration_count' => 0,
            'time_value' => 0,
            'is_free' => 0,
            'is_apply' => 0,
            'status' => 0,
        ]);
        SignInfo::whereIn('user_id', $id_arr)->update([
            'status' => SignInfoRepository::NORMAL_SIGN,
        ]);
        event(new TriggerWarning('12点半初始化成功，初始化人数：'.count($id_arr)));
    }

    /**
     * 消息有队列，需要重新设置supervisor来刷新队列
     *
     * 月末计算数据
     * 1. 将付费的失败用户的付款状态变更为close
     * 2. 给失败的用户发送模板消息
     * 3. 给没有失败也没有成功（补签过）的用户退还押金且发送模板消息（建议手动查看一下数据后执行）
     * 4. 给成功但是没有报名下一轮的用户退款
     * 5. 结算用户的本月奖金
     * 6. 给用户发送奖金到账的客服消息
     * 7. 月总结数据 (每月月末最后一天手动执行)
     * 8. 初始化新一轮打卡数据 （每月第一天00:30自动执行）
     */
    public function everyMonthSettleData()
    {
        $this->monthEndCloseUser();
        $this->failUser();
        $this->notFailUser();
        $this->successButNotApply();
        //手动调用signController的奖金瓜分函数
        $this->lucky();
        $this->everyMonthSummary();
    }

    /**
     * 针对重复报名的用户进行押金退款
     *
     * @param $id 交易单号
     * @date 2018-03-27
     */
    public function refundApply($id)
    {
        $deal = SignDeal::where('out_trade_no', $id)->first();
        $sign_deal = new SignDealRepository(new SignDeal());
        if ($deal) {
            $sign_deal->refund($deal);
            $this->info('successful');
        }
        $user = $deal->user;
        $staff = app('wechat')->staff;
        $text = new Text();
        $text->content = '您好，系统检测到您重复报名了本轮的奖金报名，现在将您多缴纳的押金退回，请注意查收微信退款的相关信息。';
        $staff->message($text)->to($user->openid)->send();
    }

    public function continueApply()
    {
        $prev_users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', 2);
        })->get();
        $next_users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', 3);
        })->get();
        $count = 0;
        foreach ($prev_users as $prev_user) {
            foreach ($next_users as $next_user) {
                if ($prev_user->id == $next_user->id) {
                    $count++;
                }
            }
        }
        $this->info($count);
    }

    public function free2deposit()
    {
        $sign_info_repository = new SignInfoRepository(new SignInfo());
        $users = $sign_info_repository->getFree2DepositUsers();
        foreach ($users as $user) {
            $this->info($user->id);
            event(new TriggerSignEvent($user, 'free2deposit'));
        }
    }

    public function nowReward()
    {
        $sign_deal_repository = new SignDealRepository(new SignDeal());
        $this->info($sign_deal_repository->getOpeningTimerProbablyFailFee());
    }

    /**
     * 发送
     */
    public function allNotice()
    {
        $user = User::find(154);
        event(new TriggerSignEvent($user, 'failRemind'));
        event(new TriggerSignEvent($user, 'rewardRemind'));
        event(new TriggerSignEvent($user, 'notFailRemind'));
        event(new TriggerSignEvent($user, 'free2deposit'));
        event(new TriggerSignEvent($user, 'applyRemind'));
        event(new TriggerSignEvent($user, 'applySuccess'));
    }

    /**
     * 初始化渠道报名的缓存
     * date: 2018-03-27
     */
    public function initCacheCounter()
    {
        Cache::forever('sign_apply_1', 0);
        Cache::forever('sign_apply_2', 0);
        Cache::forever('sign_apply_3', 0);
        Cache::forever('sign_apply_4', 0);
        Cache::forever('sign_apply_5', 0);
        Cache::forever('sign_apply_self', 0);
        Cache::forever('sign_apply_success_1', 0);
        Cache::forever('sign_apply_success_2', 0);
        Cache::forever('sign_apply_success_3', 0);
        Cache::forever('sign_apply_success_4', 0);
        Cache::forever('sign_apply_success_5', 0);
        Cache::forever('sign_apply_success_self', 0);
    }

    /**
     * 获取渠道报名的缓存信息
     * date: 2018-03-27
     */
    public function getCacheCounter()
    {
        $this->info('渠道1扫码人数:'.Cache::get('sign_apply_1'));
        $this->info('渠道2扫码人数:'.Cache::get('sign_apply_2'));
        $this->info('渠道3扫码人数:'.Cache::get('sign_apply_3'));
        $this->info('渠道4扫码人数:'.Cache::get('sign_apply_4'));
        $this->info('渠道5扫码人数:'.Cache::get('sign_apply_5'));
        $this->info('渠道个人扫码人数:'.Cache::get('sign_apply_self'));
        $this->info('渠道1报名人数:'.Cache::get('sign_apply_success_1'));
        $this->info('渠道2报名人数:'.Cache::get('sign_apply_success_2'));
        $this->info('渠道3报名人数:'.Cache::get('sign_apply_success_3'));
        $this->info('渠道4报名人数:'.Cache::get('sign_apply_success_4'));
        $this->info('渠道5报名人数:'.Cache::get('sign_apply_success_5'));
        $this->info('渠道个人报名人数:'.Cache::get('sign_apply_success_self'));
    }

    /**
     * 老用户获取报名九折卡
     *
     * @date 2018-03-27
     * @author timx
     */
    public function usersAttainCard()
    {
        $users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', (SignTimerRepository::self()->getOpeningTimer())->id);
        })->get();
        $this->info(count($users));
        $card = Card::where('id', 5)->first();
        $sign_card_repository = new SignCardRepository(new SignCard());
        foreach ($users as $user) {
            $sign_card_repository->attainCard($card, $user);
        }
    }

    /**
     * 发送模板消息给还没参与新一轮打卡的老用户
     * date: 2018-03-27
     */
    public function applyOldRemind()
    {
        $apply_timer = SignTimerRepository::static_getApplyingTimer();
        $open_timer = SignTimerRepository::static_getOpeningTimer();
        $users = User::whereHas('signApplies', function ($query) use ($apply_timer, $open_timer) {
            $query->where('timer_id', $open_timer->id)->where('timer_id', '<>', $apply_timer->id);
        })->with('detail')->get();
        $apply_users = User::whereHas('signApplies', function ($query) use ($apply_timer) {
            $query->where('timer_id', $apply_timer->id);
        })->get();
        foreach ($users as $key => $user) {
            foreach ($apply_users as $apply_key => $apply_user) {
                if ($user->id == $apply_user->id) {
                    unset($users[$key]);
                    unset($apply_users[$apply_key]);
                    break;
                }
            }
        }
        $count = 0;
        $this->info(count($users));
        foreach ($users as $user) {
            if ($user->detail->subscribe) {
                $count++;
                event(new TriggerSignEvent($user, 'applyRemind'));
//                event(new TriggerSignEvent($user, 'applyRemindMessage'));
            }
        }
        $this->info($count);
//        $this->info(count($apply_users));
//        $this->info(count($users));
//        foreach ($users as $user) {
//            event(new TriggerSignEvent($user, 'applyRemind'));
//        }
//        $user = User::find(154);
//        event(new TriggerSignEvent($user, 'applyRemind'));
//        event(new TriggerSignEvent($user, 'applyRemindMessage'));
//        $user = User::find(556);
//        event(new TriggerSignEvent($user, 'applyRemind'));
    }

    /**
     * 给有奖金可以提取的用户发送客服消息
     * date: 2018-03-27
     */
    public function lucky()
    {
        $users = User::whereHas('sign_info', function ($query) {
            $query->where('reward', '>', 0)
            ->where('status', SignInfoRepository::NORMAL_SIGN);
        })->get();
        $this->info(count($users));
        foreach ($users as $user) {
            $this->info($user->id);
            event(new TriggerSignEvent($user, 'rewardMessage'));
        }
//        $user = User::find(154);

    }

    /**
     * 打印免费打卡成功的用户
     * date: 2018-03-27
     */
    public function successFreeUsers()
    {
        $users = User::whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN);
        })->whereHas('signApplies', function ($query) {
            $query->where('timer_id', 4)->where('is_free', 1);
        })->get();
        dd($users);
    }



    public function testWeek()
    {
        $user = User::find(49);
        $signMedalRepository = new SignMedalRepository();
//        $signMedalRepository->attainMedal('gold', 1, $user);
        $sign_service = new SignService();
//        $signMedalRepository->testWeekPoster($user);
        $sign_service->sendImageWithDelete(public_path('images/sign/week/'.$user->id.'.jpeg'), $user);

    }

    /**
     * 打印参与打卡但是取关的用户
     * date: 2018-03-27
     */
    public function applyAfterCancelSubscribe()
    {
        $users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', 3)->where('is_free', 0);
        })->whereHas('detail', function ($query) {
            $query->where('subscribe', 0);
        })->with('detail')->with(['signApplies' => function ($query) {
            $query->where('timer_id', 3);
        }])->get()->toArray();
        dd(count($users));
    }






}