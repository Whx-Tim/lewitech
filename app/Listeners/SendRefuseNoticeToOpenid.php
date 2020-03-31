<?php

namespace App\Listeners;

use App\Events\RefuseDemand;
use App\Events\TriggerWarning;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendRefuseNoticeToOpenid
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
     * @param  RefuseDemand  $event
     * @return void
     */
    public function handle(RefuseDemand $event)
    {
        $wechat = app('wechat');
        $data = [
            'first' => '您好，以下是您提交的需求发布审核结果',
            'keyword1' => '用户需求',
            'keyword2' => $event->demand->title,
            'keyword3' => $event->demand->created_at->format('Y年m月d日 H:i'),
            'keyword4' => '未通过',
            'remark'   => '请点击本条消息查看详情                            您未通过审核的原因可能为：
1.发布的需求内容包含商业宣传元素；
2.发布的需求内容包含业务推广；
3.发布的需求内容不雅或触犯相关法律法规；
4.发布的需求内容信息不完善。
请您核实以上几点后，修改需求发布内容，重新提交审核。
    感谢您对乐微科技的支持~'
        ];

        try {
            $url = $event->demand->detailUrl();
            $wechat->notice->uses('4K9h0BjzcF_4zGJmbblpfViAPdG7DMSjyH1qYIso73k')->andData($data)->andReceiver($event->openid)->withUrl($url)->send();
            Log::info('refuseNoticeSuccessful: '. $event->openid);
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('拒绝用户失败，请查看系统日志，时间：'.Carbon::now()->toDateTimeString()));
        }
    }
}
