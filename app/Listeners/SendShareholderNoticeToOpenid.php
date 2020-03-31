<?php

namespace App\Listeners;

use App\Events\TriggerShareholderNotice;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendShareholderNoticeToOpenid implements ShouldQueue
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
     * @param  TriggerShareholderNotice  $event
     * @return void
     */
    public function handle(TriggerShareholderNotice $event)
    {
        call_user_func_array([new self(), $event->type], [$event->openid, $event->data]);
    }

    protected function applyActive($openid, $data)
    {
        try {
            $wechat = app('wechat');
            $wechat_data = [
                'first'    => '报名信息：姓名，电话',
                'keyword1' => '乐微科技第一期共享晚餐活动',
                'keyword2' => '2017年8月04日晚18：30开始',
                'keyword3' => '深圳大学龙岗创新研究院',
                'remark'   => '一、18:30-19:30——乐微科技季度工作汇报；
二、19:30-20:30——第一期共享晚餐活动开启；
三、20:30-21:00——给七、八月生日的来宾庆祝并合影留念。'
            ];
            $url = 'http://wx.lewitech.cn/wechat/active/detail/18';
            Log::warning('ShareholderActiveNotice:' .$openid);
            $wechat->notice->uses('7sLXrx1IAAu6E1Zs84dPzV8kiIc1xbLSdMcEebZilIk')->andData($wechat_data)->andReceiver($openid)->withUrl($url)->send();
            Log::warning('ShareholderActiveNoticeSuccessful:' .$openid);
        } catch (\Exception $exception) {
            Log::warning($exception);
        }

    }

    protected function getup($openid, $data)
    {
        try {
            $wechat = app('wechat');
            $wechat_data = [
                'first'    => '亲爱的用户，恭喜你完成本轮签到！本月签到已经结束，我们为您准备了签到打卡奖状，请点击排行榜查看哦！',
                'keyword1' => '签到打卡',
                'keyword2' => '6月27日至7月27日',
                'remark'   => '下月签到明天即将开始，快叫上小伙伴一起早起吧！'
            ];

            $url = url('wechat/getup/index');
            Log::warning('ShareholderActiveNotice:' .$openid);
            $wechat->notice->uses('a236_ieE2QjCa60RjkAWCxVp_qCYA9EyswBYgPCH7SM')->andData($wechat_data)->andReceiver($openid)->withUrl($url)->send();
            Log::warning('ShareholderActiveNoticeSuccessful:' .$openid);
        } catch (\Exception $exception) {
            Log::warning($exception);
        }
    }

    protected function suggest($openid, $data)
    {
        try {
            $wechat = app('wechat');
            $wechat_data = [
                'first'    => '亲爱的股东，app已经正式上架了，现在可以进行内测使用了',
                'keyword1' => '内测建议',
                'keyword2' => 'app内测',
                'keyword3' => '2017年8月15日',
                'remark'   => 'app已经上架了，ios的股东用户请去app store搜索"校友共享圈"，安卓的股东用户请去各自的应用市场搜索"校友共享圈"，诚邀各位股东参与到本次内测当中！
点击本条消息进入内测建议反馈通道~'
            ];

            $url = route('wechat.report.app_test');
            Log::warning('ShareholderActiveNotice:' .$openid);
            $wechat->notice->uses('r0UhDyiL22zmrejSbB3a7X6e3KRpuf7FW1m-4THGjD0')->andData($wechat_data)->andReceiver($openid)->withUrl($url)->send();
            Log::warning('ShareholderActiveNoticeSuccessful:' .$openid);
        } catch (\Exception $exception) {
            Log::warning($exception);
        }
    }
}
