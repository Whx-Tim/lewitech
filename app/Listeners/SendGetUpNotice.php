<?php

namespace App\Listeners;

use App\Events\TriggerGetUpNotice;
use App\Events\TriggerWarning;
use App\Models\GetUp;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendGetUpNotice implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TriggerGetUpNotice  $event
     * @return void
     */
    public function handle(TriggerGetUpNotice $event)
    {
        call_user_func_array([new self(),$event->type], [$event->openid, $event->getUp]);
    }

    public function today($openid, $getUp)
    {
        $app = app('wechat');
        $data = [
            'first' =>  '新的一天，新的挑战。
忙碌的你忘记打卡了吗？
',
            'keyword1' => '上午10:00前',
            'keyword2' => $getUp->day_duration,
            'keyword3' => $getUp->day_sum,
            'remark'   => '★点击底部菜单“互动互助”-“早起打卡”，继续加油吧！
☆戳下面↓还能查看“早起排行榜”。'
        ];

        Log::info('早起打卡openid: '. $openid);
        try {
            $url = url('/wechat/getup/index');
            $app->notice->uses('afNPYiCop7WZrcz3_4JoPqCEJQfLm9nBqk0fTWLcDAc')->andData($data)->andReceiver($openid)->withUrl($url)->send();
            Log::info('早起打卡发送成功openid: '. $openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function hadApply($openid, $getUp)
    {
        $app = app('wechat');
        $day = Carbon::parse($getUp->last_get_up_datetime)->diffInDays(Carbon::today());
        $data = [
            'first' =>  '亲爱的'.$getUp->user->detail->nickname.',距离您上次早起打卡已经过去'.$day.'天啦，好习惯需要坚持呀！',
            'keyword1' => $getUp->day_duration.'天',
            'keyword2' => '上午10点',
            'remark'   => '好习惯要坚持呀！点击底部菜单“互动互助”-“早起打卡”，一起来加油吧！'
        ];

        Log::info('早起打卡openid: '. $openid);
        try {
            $url = url('/wechat/getup/index');
            $app->notice->uses('kFSnVYLcbgXCVPtII4JWmJS-pYgwH3OQNEOhEMpDnJI')->andData($data)->andReceiver($openid)->withUrl($url)->send();
            Log::info('早起打卡提醒发送成功openid: '. $openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡提醒消息发送异常，请查看日志'));
            Log::warning($exception);
        }
    }

    public function all($openid, $getup)
    {
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

    public function everyDay($openid, $getup)
    {
        $app = app('wechat');
        $today_time = Carbon::parse($getup->last_get_up_datetime)->toTimeString();
        $today_date = Carbon::parse($getup->last_get_up_datetime)->toDateString();
        $data = [
            'first' => [
                'value' => '哇！早起打卡成功，给你点赞！

',
                'color' => '#000000'
            ],
            'keyword1' => $getup->day_duration.'天',
            'keyword2' => $today_time,
            'keyword3' => $today_date,
            'remark'   => [
                'value' => '★打卡开放时间：每天上午5:00~10:00
☆戳下面↓查看"早起排行榜"，继续加油喔！',
                'color' => '#000000'
            ]
        ];

        Log::info('早起打卡成功提醒openid:'.$openid);
        try {
            $url = url('/wechat/getup/index');
            $app->notice->uses('rvOhmHAHZ5fiJosF0dX4_SU0rpuiAN3NGxMFM9igH44')->andData($data)->andReceiver($openid)->withUrl($url)->send();
            Log::info('早起打卡成功提醒成功openid: '. $openid);
        } catch (\Exception $exception) {
            event(new TriggerWarning('早起打卡成功提醒发送异常，请查看日志'));
            Log::warning($exception);
        }
    }
}
