<?php

namespace App\Models;

use App\Repositories\SignInfoRepository;
use App\Repositories\SignRepository;
use App\Repositories\SignShareRepository;
use App\Repositories\SignTimerRepository;
use EasyWeChat\Message\News;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WechatText extends Model
{
    public static function handle($message)
    {
        return call_user_func([new self(), static::StringToMethod($message->Content)], $message);
    }

    public static function StringToMethod($string)
    {
        if ((int)$string) {
            return 'signShare';
        }
        if (str_contains($string, '圣诞')) {
            return 'redHat';
        }
        $world_array = ['世界杯', '头像', '冠军', '慌', '生成', '梅西'];
        if (str_contains($string, $world_array)) {
            return 'worldCup';
        }
        switch ($string) {
            case '乐微共享课堂':
            case '共享课堂':
                return 'shareClassroom';
            case '需求':
            case '需求发布':
            case '发布需求':
                return 'demand';
            default:
                return 'defaultHandle';
        }
    }

    public function worldCup()
    {
        $news = new News([
            'title' => '安排上了！原来世界杯冠军早已确定？！',
            'description' => '好玩！自定义生成世界杯头像和趣图，你也来试试！',
//            'url' => route('wechat.badge.world.index'),
            'url' => 'https://mp.weixin.qq.com/s/n9_5V2q-iLhVjwnwyROvvA',
            'image' => 'http://wj.qn.h-hy.com/images/lewitech/badge/world/world_banner.jpeg'
        ]);

        return $news;
    }

    public function redHat($message)
    {
        $news = new News([
            'title' => '校友共享圈圣诞帽装饰系统',
            'description' => '一起来给自己的头像加个圣诞帽吧~',
            'url' => route('wechat.red_hat'),
            'image' => asset('images/logo900.png')
        ]);

        return $news;
    }

    public function demand($message)
    {
        return '<a href="'. url('wechat/demand/publish') .'">需求发布</a>';
    }

    public function signShare($message)
    {
        return '';
        $id = (int)$message->Content;
        $openid = $message->FromUserName;
        DB::beginTransaction();
        $form_user = User::where('id', $id)->first();
        $to_user = User::where('openid', $openid)->first();
        $sign_timer_repository = new SignTimerRepository(new SignTimer());
        $sign_share_repository = new SignShareRepository(new SignShare());
        $sign_info_repository  = new SignInfoRepository(new SignInfo());
        $timer = $sign_timer_repository->getOpeningTimer();

        if ($form_user->sign_info == SignInfoRepository::FIRST_LOST_SIGN) {
            if ($sign_share_repository->firstHelpEnough($form_user, $timer)) {
                $is_help = $form_user->sign_shares()->where('type', SignShareRepository::FIRST_HELP)->where('help_id', $to_user->id)->where('sign_timer_id', $timer->id)->first();
                if ($is_help) {
                    $result = '您已经帮助保证了';
                    goto result;
                } else {
                    $form_user->sign_shares()->create([
                        'type' => SignShareRepository::FIRST_HELP,
                        'help_id' => $to_user->id,
                        'sign_timer_id' => $timer->id
                    ]);
                    if ($sign_share_repository->firstCanRecover($form_user, $timer)) {
                        $sign_info_repository->recoverSign($form_user);
                    }

                    $result = '帮助成功，<a href="">点击查看</a>还有谁帮助了您的好友';
                    goto result;
                }
            }

            $result = '您的好友已经补签完成了';
            goto result;
        } else if ($form_user->sign_info == SignInfoRepository::SECOND_LOST_SIGN) {
            if ($sign_share_repository->secondHelpEnough($form_user, $timer)) {
                $is_help = $form_user->sign_shares()->where('type', SignShareRepository::SECOND_HELP)->where('help_id', $to_user->id)->where('sign_timer_id', $timer->id)->first();
                if ($is_help) {
                    $result =  '您已经帮助保证了';
                    goto result;
                } else {
                    $form_user->sign_shares()->create([
                        'type' => SignShareRepository::SECOND_HELP,
                        'help_id' => $to_user->id,
                        'sign_timer_id' => $timer->id
                    ]);
                    if ($sign_share_repository->secondCanRecover($form_user, $timer)) {
                        $sign_info_repository->recoverSign($form_user);
                    }

                    $result = '帮助成功，<a href="">点击查看</a>还有谁帮助了您的好友';
                    goto result;
                }
            }

            $result = '您的好友已经补签完成了';
            goto result;
        } else {
            $result = '您的好友目前不需要补签';
            goto result;
        }
        result:
        DB::commit();

        return $result;
    }

    public function defaultHandle($message)
    {
        return '';
    }

    public function shareClassroom($message)
    {
        $new = new News([
            'title' => '乐微共享课堂',
            'url' => 'http://mp.weixin.qq.com/mp/homepage?__biz=MzUyODAwOTAzNA==&hid=4&sn=c21312bc71097c5fb5f79f9eaae918a0#wechat_redirect',
            'description' => '乐微科技全心打造《乐微共享课堂》，与你分享互联网浪潮中的智慧火花',
            'image' => asset('images/share/new_background.jpg')
        ]);

        return $new;
    }
}
