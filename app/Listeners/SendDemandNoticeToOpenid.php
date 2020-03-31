<?php

namespace App\Listeners;

use App\Events\TriggerDemandNotice;
use App\Events\TriggerWarning;
use Carbon\Carbon;
use EasyWeChat\Core\Exception;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendDemandNoticeToOpenid implements ShouldQueue
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
     * @param  TriggerDemandNotice  $event
     * @return void
     */
    public function handle(TriggerDemandNotice $event)
    {
        $app = app('wechat');
        $data = [
            'first' =>  '校友圈用户向您发布了需求帮助，请快来帮助吧~',
            'keyword1' => $event->title,
            'keyword2' => '已发布',
            'remark'   => '请点击本条消息查看需求详情'
        ];
//        $data = [
//            'first'    => '【股东需求】'.$event->demand->title,
//            'keyword1' => '股东需求',
//            'keyword2' => '乐微股东',
//            'keyword3' => $event->demand->title,
//            'keyword4' => Carbon::now()->toDateTimeString(),
//            'remark'   => '请点击本条消息查看需求详情'
//        ];

        Log::info('openid:' . $event->openid);
        try {
            $url = $event->url;
//            $app->notice->uses('UWSiuGiKtG3awOoGXI3uK7Rko8uLPmRXJZQlWeZOims')->andData($data)->andReceiver($event->openid)->withUrl($url)->send();
            $app->notice->uses('ZkgUVUWmlkvmHBXJNQou9VUOcWIGWuc9A4UMdMB0zkk')->andData($data)->andReceiver($event->openid)->withUrl($url)->send();
            Log::info('sendNoticeSuccessful:' . $event->openid);
        } catch (\EasyWeChat\Core\Exceptions\HttpException $exception) {
            Log::warning($exception);
            event(new TriggerWarning('股东需求模板消息错误！请查看系统日志！'));
        }

    }
}
