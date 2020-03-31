<?php

namespace App\Listeners;

use App\Events\TriggerCheckDemandNotice;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCheckNoticeToOpenid
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
     * @param  TriggerCheckDemandNotice  $event
     * @return void
     */
    public function handle(TriggerCheckDemandNotice $event)
    {
        $app = app('wechat');
        $data = [
            'first' => '股东需求发布审核通知',
            'keyword1' => '审核通知',
            'keyword2' => '审核管理员',
            'keyword3' => $event->username.'发起一次股东需求，请赶紧审核吧',
            'keyword4' => Carbon::now()->toDateTimeString(),
            'remark'   => '点击本条消息进行需求审核',
        ];
        $url = $event->demand->detailUrl();
        $app->notice->uses('UWSiuGiKtG3awOoGXI3uK7Rko8uLPmRXJZQlWeZOims')->andData($data)->andReceiver($event->openid)->andUrl($url)->send();
    }
}
