<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\viewPage' => [
            'App\Listeners\viewIncrement',
        ],
        'App\Events\ScanUmbrellaCode' => [
            'App\Listeners\QRCodeScanIncrement'
        ],
        'App\Events\TriggerWarning' => [
            'App\Listeners\SendWaringMail'
        ],
        'App\Events\TriggerDemandNotice' => [
            'App\Listeners\SendDemandNoticeToOpenid'
        ],
        'App\Events\TriggerCheckDemandNotice' => [
            'App\Listeners\SendCheckNoticeToOpenid'
        ],
        'App\Events\TriggerHelpDemandNotice' => [
            'App\Listeners\SendHelpNoticeToOpenid'
        ],
        'App\Events\UserViewPage' => [
            'App\Listeners\AddUserViewRelation'
        ],
        'App\Events\WechatUserLogin' => [
            'App\Listeners\WechatLogin'
        ],
        'App\Events\TriggerGetUpNotice' => [
            'App\Listeners\SendGetUpNotice'
        ],
        'App\Events\RefuseDemand' => [
            'App\Listeners\SendRefuseNoticeToOpenid'
        ],
        'App\Events\TriggerShareholderNotice' => [
            'App\Listeners\SendShareholderNoticeToOpenid'
        ],
        'App\Events\TriggerUmbrellaNotice' => [
            'App\Listeners\SendUmbrellaNoticeToOpenid'
        ],
        'App\Events\SendPhoneCode' => [
            'App\Listeners\SendCodeSMSToPhone'
        ],
        'App\Events\SendSMS' => [
            'App\Listeners\SendSMSToPhone'
        ],
        'App\Events\TriggerSignEvent' => [
            'App\Listeners\SendSignNotice'
        ],
        'App\Events\SendWeatherReportToUser' => [
            'App\Listeners\GenerateAndSendImage'
        ],
        'App\Events\UserApplySign' => [
            'App\Listeners\GenerateApplySharePoster'
        ],
        'App\Events\UserCombineAvatar' => [
            'App\Listeners\GenerateBadgeUserShareImage'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
