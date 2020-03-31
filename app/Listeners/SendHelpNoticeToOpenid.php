<?php

namespace App\Listeners;

use App\Events\TriggerHelpDemandNotice;
use App\Events\TriggerWarning;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendHelpNoticeToOpenid
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
     * @param  TriggerHelpDemandNotice  $event
     * @return void
     */
    public function handle(TriggerHelpDemandNotice $event)
    {
        $app = app('wechat');
        $data = [
            'first'    => '您发布的需求有人提供了帮助',
            'keyword1' => $event->demand->title,
            'keyword2' => Carbon::now()->toDateTimeString(),
            'remark'   => '请点击本条消息查看谁向您提供了帮助'
        ];
//        $data = [
//            'first'    => '【股东需求】',
//            'keyword1' => '帮助提醒',
//            'keyword2' => '乐微股东',
//            'keyword3' => '有股东向您提供了帮助',
//            'keyword4' => Carbon::now()->toDateTimeString(),
//            'remark'   => '请点击本条消息查看有哪些股东提供了帮助'
//        ];

        try {
            $url = $event->demand->detailUrl();
//            $app->notice->uses('UWSiuGiKtG3awOoGXI3uK7Rko8uLPmRXJZQlWeZOims')->andData($data)->andReceiver($event->demand->user->openid)->withUrl($url)->send();
            $app->notice->uses('fffHtBoDPB6HqzKMaNShJ9KV4Czv0xhC4IjHUtuYAD8')->andData($data)->andReceiver($event->demand->user->openid)->withUrl($url)->send();
            Log::info('SendNoticeSuccessful!------Help :'. $event->demand->user->openid);
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('股东需求帮助模板消息错误！请查看系统日志！'));
        }
    }
}
