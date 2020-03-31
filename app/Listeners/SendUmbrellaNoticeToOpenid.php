<?php

namespace App\Listeners;

use App\Events\SendSMS;
use App\Events\TriggerUmbrellaNotice;
use App\Events\TriggerWarning;
use App\Models\SignDonate;
use App\Models\UmbrellaHistory;
use App\Repositories\NoticeRepository;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Message\Image;
use EasyWeChat\Message\Text;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendUmbrellaNoticeToOpenid implements ShouldQueue
{
    /**
     * @var Application
     */
    private $wechat;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->wechat = app('wechat');
    }

    /**
     * Handle the event.
     *
     * @param  TriggerUmbrellaNotice  $event
     * @return void
     */
    public function handle(TriggerUmbrellaNotice $event)
    {
        call_user_func_array([new self(), $event->type], [$event->openid, $event->result]);
    }

    /**
     * 归还雨伞时的模板消息
     *
     * @param $openid
     * @param $result
     */
    protected function still($openid, $result)
    {
        $wechat = app('wechat');
        if (!is_null($result['user'])) {
            $user = $result['user'];
            try {
                $data = [
                    'first' => '归还共享雨伞',
                    'keyword1' => Carbon::parse($user->umbrellaInfo->still_at)->toDateTimeString(),
                    'keyword2' => $user->umbrellaInfo->standing(),
                    'keyword3' => '免费',
                    'keyword4' => '共享雨伞',
                    'remark'   => '欢迎再次使用共享雨伞'
                ];
                if ($openid == 'oSiVJ0s1VNlyopzRrJZL4oCHbVVQ') {

                }

                Log::warning('sendUmbrellaStillNotice:'. $openid);
                $wechat->notice->uses('oCzEd8_n9SIlLhiW5wxGl1hkNQwNl9wIixYotT58yA0')->andData($data)->andReceiver($openid)->send();
                Log::warning('sendSuccessful');
            } catch (\Exception $exception) {
                Log::warning($exception);
                event(new TriggerWarning('地铁归还模板消息出现bug，请检查相关日志，异常用户id为'.$user->id.',openid为：'.$openid));
            }
        }
    }

    /**
     * 还伞时的客服消息发送
     *
     * @param $openid
     * @param $result
     */
    protected function stillNew($openid, $result)
    {
        $wechat = app('wechat');
        if (!is_null($history = $result['history'])) {
            $text = new Text();
            $text->content = '还伞成功！感谢您对共享雨伞的支持~您的每次用伞都离不开爱心人士的捐赠，请继续支持我们哦！
        <a href="'. route('wechat.pay.response.umbrella.gratuity') .'">去打赏</a>         <a href="'. route('wechat.umbrella.history.detail', ['id' => $history->id]) .'">查看还伞信息</a>';
            $staff = $wechat->staff;
            $staff->message($text)->to($openid)->send();
        }
//        if (!is_null($result['user'])) {
//            try {
//                $user = $result['user'];
//                $url = 'http://mp.weixin.qq.com/s/as9jg72grHcNUkdA0FTtEw';
//                $data = [
//                    'first' => '还伞成功！
//
//您的用伞信息如下：',
//                    'keyword1' => $user->umbrellaInfo->standing(),
//                    'keyword2' => '免费',
//                    'keyword3' => Carbon::parse($user->umbrellaInfo->still_at)->toDateString(),
//                    'remark'   => [
//                        'value' => '★太棒了！诚信是一种美德，送您一朵✿小花儿，为您按时还伞点赞！欢迎再来借伞哦！
//☆下次可以试试另一种酷炫的还伞方法，把伞随时随地“传递”给身边有需要的小哥哥小姐姐。
//★如何“传递”↓请戳我查看详情。' ,
//                        'color' => '#000000'
//                    ]
//                ]
//                ;
//
//                Log::warning('sendUmbrellaStillNotice:'. $openid);
//                $wechat->notice->uses('sKdzabUZpEwA6RglvqUWn-mKyNOd1jb7TgnZv2ZVWTM')->andData($data)->andReceiver($openid)->withUrl($url)->send();
//                Log::warning('sendSuccessful');
//            } catch (\Exception $exception) {
//                Log::warning($exception);
//                event(new TriggerWarning('地铁归还雨伞出现bug，请检查相关日志，时间：'.Carbon::now()->toDateTimeString()));
//            }
//        }
    }

    /**
     * 公益爱心伞最后一天还未归还的模板消息
     *
     * @param $openid
     * @param $result
     */
    protected function lastStill($openid, $result)
    {
        $wechat = app('wechat');
        $user = $result['user'];
        if (!is_null($result['user'])) {
            try {
                $url = 'http://mp.weixin.qq.com/s/as9jg72grHcNUkdA0FTtEw';
                $data = [
                    'first'    => '亲爱的共享雨伞用户

您有一把伞逾期未还，请最迟于'. $user->umbrellaInfo->lastStillDate() .'还伞',
                    'keyword1' => $user->umbrellaInfo->standing(),
                    'keyword2' => '免费',
                    'keyword3' => $user->umbrellaInfo->lastStillDate(),
                    'remark'   => [
                        'value' => '★诚信是一种美德。请您按约定还伞，否则您的账号将进入黑名单无法再借伞。',
                        'color' => '#000000'
                    ]
                ];
//                if ($openid == 'oSiVJ0s1VNlyopzRrJZL4oCHbVVQ') {
//                    $data = [
//                        'first'    => '亲爱的共享雨伞用户
//
//您有一把伞逾期未还，请最迟于'. $user->umbrellaInfo->lastStillDate() .'还伞
//★诚信是一种美德。请您按约定还伞，否则您的账号将进入黑名单无法再借伞。',
//                        'keyword1' => '14天',
//                        'keyword2' => '免费',
//                        'keyword3' => '明日下午6点前',
//                        'remark'   => [
//                            'value' => '感谢乐微科技的冠名支持，风雨兼程，与你同在（广告文字），点我查看广告详情',
//                            'color' => '#000000'
//                        ]
//                    ];
//                }


                $user->noticeHistories()->create([
                    'type' => 'umbrella_last',
                    'status' => 1,
                ]);
                Log::warning('sendUmbrellaStillNotice:' . $openid . $user->umbrellaInfo->standing());
                $wechat->notice->uses('sKdzabUZpEwA6RglvqUWn-mKyNOd1jb7TgnZv2ZVWTM')->andData($data)->andReceiver($openid)->withUrl($url)->send();
                Log::warning('sendSuccessful');
            } catch (\Exception $exception) {
                Log::warning($exception);
                $user->noticeHistories()->create([
                    'type' => 'umbrella_last',
                    'data' => '雨伞最后期限归还模板消息发送失败'
                ]);
                event(new TriggerWarning('地铁归还雨伞出现bug，改发短信提醒，请检查相关日志，时间：' . Carbon::now()->toDateTimeString()));
                event(new SendSMS($user->phone, '明日', 'still'));
            }
        }
    }

    /**
     * 公益爱心伞12天未归还时进行模板消息提醒
     *
     * @param $openid
     * @param $result
     */
    protected function remindStill($openid, $result)
    {
        $wechat = app('wechat');
        $user = $result['user'];
        if (!is_null($result['user'])) {
            try {
                $url = 'http://mp.weixin.qq.com/s/as9jg72grHcNUkdA0FTtEw';
                $data = [
                    'first' => '亲爱的共享雨伞用户

请记得前往共享雨伞借伞地点还伞哦~',
                    'keyword1' => $user->umbrellaInfo->standing(),
                    'keyword2' => '免费',
                    'keyword3' => $user->umbrellaInfo->shouldStillDate(),
                    'remark'   => [
                        'value' => '★共享雨伞可免费使用15天。逾期或不还伞将无法再次借伞。
☆另一种酷炫的还伞方法，就是把伞随时随地“传递”给身边有需要的小哥哥小姐姐——还有这种操作哦！是不是很有爱！如何“传递”↓请戳我查看详情
★如有其他疑问可加微信号weijuanpingtai来撩客服。祝您用伞愉快！',
                        'color' => '#000000'
                    ]
                ];

//                if ($openid == 'oSiVJ0s1VNlyopzRrJZL4oCHbVVQ') {
//                    $data = [
//                        'first' => '亲爱的共享雨伞用户
//
//请记得前往共享雨伞借伞地点还伞哦~
//★共享雨伞可免费使用15天。逾期或不还伞将无法再次借伞。
//★如有其他疑问可加微信号weijuanpingtai来撩客服。祝您用伞愉快！',
//                        'keyword1' => '11天',
//                        'keyword2' => '免费',
//                        'keyword3' => '15天后',
//                        'remark'   => [
//                            'value' => '感谢乐微科技的冠名支持，风雨兼程，与你同在（广告文字），点我查看广告详情',
//                            'color' => '#000000'
//                        ]
//                    ];
//                }


                Log::warning('sendUmbrellaStillNotice:'. $openid. $user->umbrellaInfo->standing());
                $wechat->notice->uses('sKdzabUZpEwA6RglvqUWn-mKyNOd1jb7TgnZv2ZVWTM')->andData($data)->andReceiver($openid)->withUrl($url)->send();
                $user->noticeHistories()->create([
                    'type' => 'umbrella_remind',
                    'status' => 1,
                ]);
                Log::warning('sendSuccessful');
            } catch (\Exception $exception) {
                Log::warning($exception);
                $user->noticeHistories()->create([
                    'type' => 'umbrella_remind',
                    'data' => '雨伞12天归还模板消息发送失败'
                ]);
                event(new TriggerWarning('地铁归还提醒模板消息出现bug，改发短信提醒，请检查相关日志，异常用户id为'.$user->id.',openid为：'.$openid));
                event(new SendSMS($user->phone, $user->umbrellaRemindStillDate(), 'stillRemind'));
            }
        }
    }

    /**
     * 公益爱心伞调查问卷
     *
     * @param $openid
     * @param $result
     */
    public function question($openid, $result)
    {
        $wechat = app('wechat');

        try {
            $url = 'https://www.wjx.cn/jq/17470009.aspx';
            $data = [
                'first' => '尊敬的共享雨伞用户，为了让共享雨伞项目能够更好地服务您，希望您能认真填写本问卷，我们将认真参考您的建议，不断优化项目，给您带来更加方便快捷的借、还伞体验！',
                'keyword1' => '共享雨伞用户满意度调查',
                'keyword2' => '2017年10月24日',
                'keyword3' => '2017年10月25日',
                'remark' => '共享雨伞，伴你轻松出行！点击详情即可进入问卷填写页面'
            ];

            Log::warning('地铁问卷调查:'. $openid);
            $wechat->notice->uses('CjzdTkTkF7OH8yFVqcMaIhbacMyhz2LgtV1mEytOfjE')->andData($data)->andReceiver($openid)->withUrl($url)->send();
            Log::warning('地铁问卷调查sendSuccessful');
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('地铁问卷调查，请检查相关日志，时间：'.Carbon::now()->toDateTimeString()));
        }
    }

    /**
     * 雨伞状态解除模板消息
     *
     * @param $openid
     * @param $result
     */
    protected function relieveNotice($openid, $result)
    {
        $wechat = app('wechat');
        $user = $result['user'];
        if (!is_null($result['user'])) {
            try {
                $data = [
                    'first' => '亲爱的共享雨伞用户',
                    'keyword1' => $user->umbrellaInfo->standing(),
                    'keyword2' => '免费',
                    'keyword3' => '已归还',
                    'remark'   => [
                        'value' => '亲爱的用户您好！我们对您的情况进行核查后，为您恢复正常状态（已清除相关记录），感谢您对共享雨伞的支持，祝您用伞愉快！',
                        'color' => '#000000'
                    ]
                ];

                Log::warning('sendUmbrellaStillNotice:'. $openid. $user->umbrellaInfo->standing());
                $wechat->notice->uses('sKdzabUZpEwA6RglvqUWn-mKyNOd1jb7TgnZv2ZVWTM')->andData($data)->andReceiver($openid)->send();
                $user->noticeHistories()->create([
                                                     'type' => 'umbrella_relieve',
                                                     'status' => 1,
                                                 ]);
                Log::warning('sendSuccessful');
            } catch (\Exception $exception) {
                Log::warning($exception);
                $user->noticeHistories()->create([
                                                     'type' => 'umbrella_relieve',
                                                     'data' => '雨伞解除状态模板消息发送失败'
                                                 ]);
            }
        }
    }

    /**
     * 捐赠后发送的爱心雨伞模板消息
     *
     * @param $openid
     * @param $result
     */
    public function donateResponse($openid, $result)
    {
        $wechat = $this->wechat;
        $umbrella = $result['umbrella'];
        $donate = $result['donate'];
//        try {
//            $data = [
//                'first' => '尊敬的捐赠人：'.$donate->name.'，您好！又有一位用户使用了您所捐赠的公益爱心伞！',
//                'keyword1' => $umbrella,
//                'keyword2' => '公益爱心伞',
//                'keyword3' => Carbon::now()->toDateTimeString(),
//                'remark' => '感谢您的这份善心，让更多有需要的人，能得到及时的帮助，希望您继续支持我们！'
//            ];
//
//            Log::warning('雨伞捐赠回应:'. $openid);
//            $user = $donate->user;
//            $notice = NoticeRepository::signDonateResponse($user);
//            $wechat->notice->uses('jQgH1dGltKepUS6rTw8BphnPHOggdq4dMlr06aosGpE')->andData($data)->andReceiver($openid)->send();
//            $notice->status = 1;
//            $notice->save();
//            Log::warning('雨伞捐赠回应sendSuccessful');
//        } catch (\Exception $exception) {
//            Log::warning($exception);
//            event(new TriggerWarning('雨伞捐赠回应，请检查相关日志，时间：'.Carbon::now()->toDateTimeString()));
//        }
    }


}
