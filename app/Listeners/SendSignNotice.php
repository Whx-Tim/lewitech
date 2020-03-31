<?php

namespace App\Listeners;

use App\Events\TriggerSignEvent;
use App\Events\TriggerWarning;
use App\Models\NoticeHistory;
use App\Models\Sign;
use App\Repositories\NoticeRepository;
use App\Repositories\SignDealRepository;
use App\Repositories\SignTimerRepository;
use Carbon\Carbon;
use EasyWeChat\Message\Text;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendSignNotice implements ShouldQueue
{
    private $notice_repository;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->notice_repository = new NoticeRepository(new NoticeHistory());
    }

    /**
     * Handle the event.
     *
     * @param  TriggerSignEvent  $event
     * @return void
     */
    public function handle(TriggerSignEvent $event)
    {
        call_user_func([new self(), $event->type], $event);
    }

    public function today($event)
    {
        $user = $event->user;
        $app = app('wechat');
        $data = [
            'first' =>  '新的一天，新的挑战。
忙碌的你忘记打卡了吗？
',
            'keyword1' => '上午10:00前',
            'keyword2' => $user->sign_info->duration_count,
            'keyword3' => $user->sign_info->total_count,
            'remark'   => '★点击底部菜单“早起打卡”，继续加油吧！
☆戳下面↓即可马上打卡还能查看“早起排行榜”。'
        ];

        Log::info('早起打卡openid: '. $user->openid);
        try {
            $url = route('wechat.sign.index');
            $notice = $this->notice_repository->sign_today($user);
            $app->notice->uses('afNPYiCop7WZrcz3_4JoPqCEJQfLm9nBqk0fTWLcDAc')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
            $notice->status = 1;
            $notice->save();
            Log::info('早起打卡发送成功openid: '. $user->openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function hadSign($event)
    {
        $app = app('wechat');
        $user = $event->user;
        $sign = $user->signs()->orderBy('today_time', 'desc')->first();
        $day = Carbon::parse($sign->today_time)->diffInDays(Carbon::today());
        $data = [
            'first' =>  '亲爱的'.$user->detail->nickname.',距离您上次早起打卡已经过去'.$day.'天啦，好习惯需要坚持呀！',
            'keyword1' => $user->sign_info->duration_count.'天',
            'keyword2' => '上午10点',
            'remark'   => '伴你早起，让奋斗不再孤单！让我们继续坚持早睡早起的好习惯吧~点击详情即可立刻打卡！'
        ];

        Log::info('早起打卡openid: '. $user->openid);
        try {
            $url = route('wechat.sign.index');
            $notice = $this->notice_repository->had_sign($user);
            $app->notice->uses('kFSnVYLcbgXCVPtII4JWmJS-pYgwH3OQNEOhEMpDnJI')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
            $notice->status = 1;
            $notice->save();
            Log::info('早起打卡提醒发送成功openid: '. $user->openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡提醒消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function all($event)
    {
        $user = $event->user;
        $openid = $user->detail->openid;
        $app = app('wechat');
        $data = [
            'first' => [
                'value' => '听说，早起的一天，时间会变多！早起让你专注力提升，效率杠杠滴~早起还能轻松躲过地铁高峰！早睡早起，身体棒棒！校友共享圈邀你一起加入早起打卡啦！

',
                'color' => '#000000'
            ],
            'keyword1' => '0天',
            'keyword2' => '暂无',
            'keyword3' => '暂未打卡',
            'remark'   => [
                'value' => '★点击底部菜单"互动互助"-"早起打卡"就能参与。
☆打卡开放时间：每天上午5:00~10:00
★打卡成功还可以查看"早起排行榜"，加油加油！',
                'color' => '#000000'
            ]
        ];

        Log::info('早起打卡模板推广openid:'.$openid);
        try {
            $url = url('/wechat/getup/index');
            $app->notice->uses('rvOhmHAHZ5fiJosF0dX4_SU0rpuiAN3NGxMFM9igH44')->andData($data)->andReceiver($openid)->withUrl($url)->send();
            Log::info('早起打卡模板推广成功openid: '. $openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡推广消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function everyDay($event)
    {
        $user = $event->user;
        $app = app('wechat');
        $sign = $user->signs()->orderBy('today_time', 'desc')->first();
        $today_time = Carbon::parse($sign->today_time)->toTimeString();
        $today_date = Carbon::parse($sign->today_time)->toDateString();
        $data = [
            'first' => [
                'value' => '哇！早起打卡成功，给你点赞！

',
                'color' => '#000000'
            ],
            'keyword1' => $user->sign_info->duration_count.'天',
            'keyword2' => $today_time,
            'keyword3' => $today_date,
            'remark'   => [
                'value' => '★打卡开放时间：每天上午5:00~10:00
☆戳下面↓查看"早起排行榜"，继续加油喔！',
                'color' => '#000000'
            ]
        ];

        Log::info('早起打卡成功提醒openid:'.$user->openid);
        try {
            $url = route('wechat.sign.index');
            $app->notice->uses('rvOhmHAHZ5fiJosF0dX4_SU0rpuiAN3NGxMFM9igH44')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
            Log::info('早起打卡成功提醒成功openid: '. $user->openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡成功提醒发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function lostSign($event)
    {
        $user = $event->user;
        $app = app('wechat');

        $data = [
            'first' =>  '亲爱的'.$user->detail->nickname.',今早是不是太忙啦？竟然忘记打卡了哦！',
            'keyword1' => $user->sign_info->duration_count.'天',
            'keyword2' => '上午10点',
            'remark'   => '早起打卡的伟业不能停，快点击我进行补签吧！'
        ];

        Log::info('早起打卡openid: '. $user->openid);
        try {
            $url = route('wechat.sign.poster.share');
            $notice = $this->notice_repository->lost_sign($user);
            $app->notice->uses('kFSnVYLcbgXCVPtII4JWmJS-pYgwH3OQNEOhEMpDnJI')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
            $notice->status = 1;
            $notice->save();
            Log::info('早起打卡提醒发送成功openid: '. $user->openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡提醒消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function week($event)
    {
        $user = $event->user;
        $app = app('wechat');

        $data = [
            'first' => '亲爱的'.$user->detail->nickname.'勇士，您的早起打卡一周战报已生成，请点击链接查收哟~',
            'keyword1' => '早起打卡周报告',
            'keyword2' => Carbon::now()->toDateString(),
            'remark' => '总结过去，才能走得更远飞得更高！各位大侠，事不宜迟，快来生成专属自己的早起打卡战报吧！'
        ];

        Log::info('早起打卡openid: '. $user->openid);
        try {
            $url = route('wechat.sign.poster.week');
            $notice = $this->notice_repository->sign_week($user);
            $app->notice->uses('_VaFhdR5-RqD0h1BJexIcYm0g95HJdqKr3vla1WGQjg')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
            $notice->status = 1;
            $notice->save();
            Log::info('早起打卡提醒发送成功openid: '. $user->openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡提醒消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function weekImage($event)
    {
        $user = $event->user;
        $app = app('wechat');
    }

    public function applySuccess($event)
    {
        $user = $event->user;
        $app = app('wechat');

        $data = [
            'first' => '恭喜你，你已报名成功！',
            'keyword1' => $user->detail->nickname,
            'keyword2' => '八月份早起打卡',
            'keyword3' => '2018年8月1日',
            'keyword4' => '2018年8月30日',
            'keyword5' => '30元',
            'remark' => '让奋斗不再孤单！期待你和你的小伙伴一起参与哦~'
        ];
//        $url = route('wechat.sign.apply.share');

        Log::info('早起打卡所有报名用户openid: '. $user->openid);
        try {
//            $url = route('wechat.sign.setting');
//            $app->notice->uses('jm-rG0z9JWW7j5wgSixdqpo9e1eb3xntDiXVlSaF13o')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
            $app->notice->uses('jm-rG0z9JWW7j5wgSixdqpo9e1eb3xntDiXVlSaF13o')->andData($data)->andReceiver($user->openid)->send();
            Log::info('早起打卡所有报名用户发送成功openid: '. $user->openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡提醒消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function applyRemind($event)
    {
        $user = $event->user;
        $app = app('wechat');

        $data = [
            'first' => '亲爱的'.$user->detail->nickname.'，Hi~早起打卡活动已经开始报名啦！“用户越多，奖金越多，乐趣多多”，快点加入早起打卡的大家庭吧！',
            'keyword1' => $user->detail->nickname,
            'keyword2' => '2018年6月30日止',
            'keyword3' => '数据统计中...',
            'remark' => '让奋斗不再孤单！点击详情即可参与报名哦！早睡早起身体好，让我们一起来坚持吧！'
        ];

        Log::info('早起打卡所有报名用户openid: '. $user->openid);
        try {
            $url = route('wechat.sign.apply');
            $app->notice->uses('y1s9Gir904z-ajcuF-qwwIydXOIOruwyBCTsclto4ns')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
            Log::info('早起打卡所有报名用户发送成功openid: '. $user->openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡提醒消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }
    public function applyRemindMessage($event)
    {
        $user = $event->user;
        $wechat = app('wechat');
        $staff = $wechat->staff;
        $text = new Text();
        $text->content = '亲爱的'.$user->detail->nickname.',七月早起打卡活动已经开始报名啦！期待与你红尘作伴哟~老用户6月30日前（含30日）报名立享九折优惠哦！
        
        <a href="'. route('wechat.sign.apply') .'">马上报名</a>      ';
        if ($user->detail->subscribe) {
            try {
                $staff->message($text)->to($user->openid)->send();
            } catch (\Exception $exception) {
                $this->applyRemind($event);
            }

        }
    }

    public function failRemind($event)
    {
        $user = $event->user;
        $app = app('wechat');

        $data = [
            'first' => '非常遗憾！您七月的打卡任务未能完成，报名时缴纳的早起基金将被系统自动扣除。',
            'keyword1' => '未能完成七月打卡任务',
            'keyword2' => '2018年7月31日',
            'remark' => '如果您对此审核有异议，可在公众号进行留言反馈。但请不要气馁，希望您在八月份继续参与，再次努力，一起赢取瓜分千元奖金的机会吧！马上点击链接进行报名。'
        ];

        Log::info('打卡失败用户: '. $user->openid);
        try {
//            $url = route('wechat.sign.apply');
            if ($user->detail->subscribe) {
                $app->notice->uses('yC0DgYd_LcQwGVJImGfZs1UQMzXE27eKc26YTfye_Z0')->andData($data)->andReceiver($user->openid)->send();
//                $app->notice->uses('yC0DgYd_LcQwGVJImGfZs1UQMzXE27eKc26YTfye_Z0')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
                Log::info('打卡失败用户成功: '. $user->openid);
            } else {
                Log::info('打卡失败用户取消关注：'. $user->openid);
            }

        } catch (\Exception $exception) {
            event(new TriggerWarning('打卡失败用户发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function rewardRemind($event)
    {
        $user = $event->user;
        $app = app('wechat');

        $data = [
            'first' => '恭喜您顺利完成了七月份的早起打卡任务！本期您可提取的奖金金额为：'. $user->sign_info->reward .'元（不含押金）！',
            'keyword1' => '2018年7月1日-7月31日',
            'keyword2' => '31天',
            'remark' => '立马点击链接进行提现吧！希望您再接再厉，八月份喊上小伙伴们共同参与早起打卡，瓜分奖金~（注：本轮奖金由任务失败用户的早起基金和乐微科技赞助奖金组成)'
        ];

        Log::info('打卡奖金用户: '. $user->openid);
        try {
//            $url = route('wechat.sign.reward');
            if ($user->detail->subscribe) {
//                $app->notice->uses('yZDVi8IXH5vPLkK-pLVCdO5jm6dmL_8adpBsOpCgdFg')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
                $app->notice->uses('yZDVi8IXH5vPLkK-pLVCdO5jm6dmL_8adpBsOpCgdFg')->andData($data)->andReceiver($user->openid)->send();
                Log::info('打卡奖金用户成功: '. $user->openid);
            } else {
                Log::info('打卡奖金用户取消关注：'. $user->openid);
            }

        } catch (\Exception $exception) {
            event(new TriggerWarning('打卡奖金用户发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function notFailRemind($event)
    {
        $user = $event->user;
        $app = app('wechat');

        try {
            $refund_fee = ($user->signDeals()->where('sign_timer_id', (SignTimerRepository::self()->getOpeningTimer())->id)->where(function ($query) {
                $query->whereIn('result_code', [SignDealRepository::SUCCESS_STATUS, SignDealRepository::CONTINUE_SUCCESS_STATUS, SignDealRepository::CONTINUE_STATUS, SignDealRepository::OVER_STATUS]);
            })->first())->total_fee;
            $refund_fee /= 100;

            $data = [
                'first' => '太可惜啦，您距离瓜分奖金就差一点点了！因此上一期的早起押金已成功退还到您的微信账号上了，请留意相关退款信息。如有疑问可在公众号留言或者添加客服“好荔友”进行咨询。',
                'keyword1' => '2018年7月31日',
                'keyword2' => $refund_fee.'元',
                'remark' => '再接再厉，期待您勇夺下一期丰厚奖金！'
            ];
//            $url = route('wechat.sign.apply');
            if ($user->detail->subscribe) {
//                $app->notice->uses('vKBaxb6yRgmIcMepOAQgTXMCW5-t_gdyY_oEFKykaZI')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
                $app->notice->uses('vKBaxb6yRgmIcMepOAQgTXMCW5-t_gdyY_oEFKykaZI')->andData($data)->andReceiver($user->openid)->send();
                Log::info('没有成功的用户: '. $user->openid);
            } else {
                Log::info('没有成功的用户取消关注：'. $user->openid);
            }

        } catch (\Exception $exception) {
            event(new TriggerWarning('没用成功的用户发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function free2deposit($event)
    {
        $user = $event->user;
        $app = app('wechat');

        Log::info('免费报名转收费报名: '. $user->openid);
        try {
            $data = [
                'first' => '亲爱的' .$user->detail->nickname. '，系统监测到你目前早起打卡的版本仍为免费版。（注：免费版是不享受月末瓜分奖金的活动的）',
                'keyword1' => '是否升级为付费版参与瓜分奖金',
                'keyword2' => '2017年12月11号上午10:00前截止',
                'keyword3' => '早起打卡报名升级提醒',
                'keyword4' => '紧急',
                'remark' => '根据您这一周的打卡情况来看，保持这个势头，是很有可能获得瓜分奖金的机会的哟，要不要挑战一下早起打卡付费版？
点击本条详情立马升级为付费版，一起来瓜分千元奖金吧！'
            ];
            $url = route('wechat.sign.apply.free2deposit');
            if ($user->detail->subscribe) {
                $app->notice->uses('USWElWvfzzAwwSgyTgtw_xJmAtYiFce_ik9usomzZfI')->andData($data)->andReceiver($user->openid)->withUrl($url)->send();
                Log::info('免费报名转收费报名用户成功: '. $user->openid);
            } else {
                Log::info('免费报名转收费报名用户取消关注：'. $user->openid);
            }

        } catch (\Exception $exception) {
            event(new TriggerWarning('打卡补签用户发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function rewardMessage($event)
    {
        $user = $event->user;
        $wechat = app('wechat');
        $staff = $wechat->staff;
        $text = new Text();
        $text->content = '亲爱的'.$user->detail->nickname.',您的七月早起打卡奖金已经到账，可在"个人中心-我的奖金"里查看，您可选择直接提现或选择捐赠，支持我们"共享雨伞"项目
        
        <a href="'. route('wechat.sign.reward') .'">提现</a>      <a href="'. route('wechat.sign.donate') .'">捐赠</a>';
        if ($user->detail->subscribe) {
            try {
                $staff->message($text)->to($user->openid)->send();
            } catch (\Exception $exception) {
                $this->rewardRemind($event);
            }
        }
    }


//    public function lostSignStaff($event)
//    {
//        $user = $event->user;
//
//    }
}
