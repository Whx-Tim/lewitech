<?php

namespace App\Models;

use App\Events\TriggerGetUpNotice;
use App\Events\TriggerUmbrellaNotice;
use App\Events\TriggerWarning;
use App\Repositories\SignInfoRepository;
use App\Repositories\SignShareRepository;
use App\Repositories\SignTimerRepository;
use Carbon\Carbon;
use EasyWeChat\Message\News;
use EasyWeChat\Message\Text;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WechatEvent
{
    public static function eventHandle($message)
    {
        if (method_exists(new self(), static::StringToMethod($message->EventKey))) {
            $method = static::StringToMethod($message->EventKey);
        } else {
            $method = 'other';
        }
        Log::alert($message->EventKey);
        return call_user_func([new self(), $method], $message);
    }

//    public function unicodeDecode($data)
//    {
//        $word = json_decode(preg_replace_callback('/&#(\d{5});/', create_function('$dec', 'return \'\\u\'.dechex($dec[1]);'), '"' . $data . '"'));
//        return $word;
//    }
//
//    public function postToSign()
//    {
//        $post = new WechatPost("orginal", "http://lz.goszu.com/tp5/public/index.php/lewei/", "szu", file_get_contents("php://input"));
//        echo $post->result();
//        exit();
//    }

    public static function StringToMethod($string)
    {
        $string = str_replace('qrscene_', '', $string);
        if (str_contains($string, 'sign_share')) {
            return 'signUser';
        }
        if (str_contains($string, 'sign_apply_')) {
            return 'sign_apply_all';
        }
        if (str_contains($string, 'umbrella_still_station_')) {
            return 'umbrella_still_station';
        }
        if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $string)) {
            switch ($string) {
                case '签到打卡':
                    return 'signCard';
                default:
                    return 'other';
            }
        } else {
            return $string;
        }

    }

    public function red_hat($message)
    {
        $news = new News([
            'title' => '校友共享圈圣诞帽装饰系统',
            'description' => '一起来给自己的头像加个圣诞帽吧~',
            'url' => route('wechat.red_hat'),
            'image' => asset('images/logo900.png')
        ]);

        return $news;
    }

    public function badge_football_world()
    {
        $news = new News([
            'title' => '世界杯 | 专属头像生成器',
            'description' => '好玩！自定义生成世界杯头像和趣图，你也来试试！',
            'url' => route('wechat.badge.world.index'),
//            'url' => 'https://mp.weixin.qq.com/s/n9_5V2q-iLhVjwnwyROvvA',
            'image' => 'http://wj.qn.h-hy.com/images/lewitech/badge/world/world_banner.jpeg'
        ]);

        return $news;
    }

    public function signUser($message)
    {
        $key = $message->EventKey;
        $id = str_replace('qrscene_sign_share_', '', $key);
        $id = str_replace('sign_share_', '', $id);
        $openid = $message->FromUserName;
        DB::beginTransaction();
        $form_user = User::where('id', $id)->with('sign_info')->first();
        if ($form_user->openid == $openid) {
            $result = '无法帮助自己补签';
            goto result;
        }
        $to_user = User::where('openid', $openid)->first();

        $sign_timer_repository = new SignTimerRepository(new SignTimer());
        $sign_share_repository = new SignShareRepository(new SignShare());
        $sign_info_repository  = new SignInfoRepository(new SignInfo());
        $timer = $sign_timer_repository->getOpeningTimer();

        if ($form_user->sign_info->status == SignInfoRepository::FIRST_LOST_SIGN) {
            if ($sign_share_repository->firstHelpEnough($form_user, $timer)) {
                $is_help = $form_user->sign_help_froms()->where('type', SignShareRepository::FIRST_HELP)->where('help_id', $to_user->id)->where('sign_timer_id', $timer->id)->first();
                if ($is_help) {
                    $result = '您已经帮助保证了';
                    goto result;
                } else {
                    $form_user->sign_help_froms()->create([
                        'type' => SignShareRepository::FIRST_HELP,
                        'help_id' => $to_user->id,
                        'sign_timer_id' => $timer->id
                    ]);
                    if ($sign_share_repository->firstCanRecover($form_user, $timer)) {
                        $sign_info_repository->recoverSign($form_user);
                    }

                    $result = '帮助成功，<a href="'. route('wechat.sign.share', ['user' => $id]) .'">点击查看</a>还有谁帮助了您的好友';
                    goto result;
                }
            }

            $result = '您的好友已经补签完成了';
            goto result;
        } else if ($form_user->sign_info->status == SignInfoRepository::SECOND_LOST_SIGN) {
            if ($sign_share_repository->secondHelpEnough($form_user, $timer)) {
                $is_help = $form_user->sign_help_froms()->where('type', SignShareRepository::SECOND_HELP)->where('help_id', $to_user->id)->where('sign_timer_id', $timer->id)->first();
                if ($is_help) {
                    $result =  '您已经帮助保证了';
                    goto result;
                } else {
                    $form_user->sign_help_froms()->create([
                        'type' => SignShareRepository::SECOND_HELP,
                        'help_id' => $to_user->id,
                        'sign_timer_id' => $timer->id
                    ]);
                    if ($sign_share_repository->secondCanRecover($form_user, $timer)) {
                        $sign_info_repository->recoverSign($form_user);
                    }

                    $result = '帮助成功，<a href="'. route('wechat.sign.share', ['user' => $id]) .'">点击查看</a>还有谁帮助了您的好友';
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

    public function other()
    {
//        Log::alert('other');
        return '';
    }

    public function sign_apply($message)
    {
        $new = new News([
            'title' => '乐微早起打卡报名',
            'url' => route('wechat.sign.apply'),
            'description' => '伴你早起，让奋斗不再孤单！让我们继续坚持早睡早起的好习惯吧~还在等什么，赶紧点击报名吧！期待你和你的小伙伴一起参与哦~',
            'image' => asset('images/sign/apply_background.png')
        ]);
        $cache = 'sign_apply_user_'.$message->FromUserName;
        Cache::put($cache, 'self', 5*24*60);
        Cache::increment('sign_apply_self');

        return $new;
    }

    public function lewitch_insurance($message)
    {
        $openid = $message->FromUserName;
        User::firstCreateOrUpdate($openid);
        $new = new News([
            'title' => '乐微保险福利',
            'url' => url('wechat/insurance'),
            'description' => '乐微保险福利',
            'image' => asset('images/logo.png')
        ]);

        return $new;
    }

    public function sign_apply_all($message)
    {
        $key = str_replace('sign_apply_', '', $message->EventKey);
        $cache = 'sign_apply_user_'.$message->FromUserName;
        Cache::put($cache, $key, 5*24*60);
        Cache::increment('sign_apply_'.$key);

        $new = new News([
            'title' => '乐微早起打卡报名',
            'url' => route('wechat.sign.apply'),
            'description' => '伴你早起，让奋斗不再孤单！让我们继续坚持早睡早起的好习惯吧~还在等什么，赶紧点击报名吧！期待你和你的小伙伴一起参与哦~',
            'image' => asset('images/sign/apply_background.png')
        ]);

        return $new;
    }

    public function umbrella_still_station($message)
    {
        $id = str_replace('qrscene_umbrella_still_station_', '', $message->EventKey);
        $id = str_replace('umbrella_still_station_', '', $id);
        if ($id == 21) {
            return '';
        }

        return $this->umbrella_still($message, $id);
    }

    public function umbrella_still($message, $station_id = 0)
    {
        $openid = $message->FromUserName;
        if ($station_id == 0) {
            return $this->forceStill($openid);
        }
        $station = UmbrellaStation::find($station_id);
        $user = User::where('openid', $openid)->first();
        if (empty($user)) {
            $new = new News([
                'title' => '点击借伞',
                'description' => '您还没在借伞平台上注册信息，请点击本条消息进行注册',
                'url' => route('wechat.umbrella.index'),
                'image' => asset('images/umbrella/index-banner.png')
            ]);

            return $new;
        }
        $now = Carbon::now()->toDateTimeString();
        try {
            if (is_null($user->phone) | is_null($user->ID_number) | is_null($user->real_name)) {
                $new = new News([
                    'title' => '点击借伞',
                    'description' => '您还没在借伞平台上注册信息，请点击本条消息进行注册',
                    'url' => route('wechat.umbrella.index'),
                    'image' => asset('images/umbrella/index-banner.png')
                ]);

                return $new;
//                $result = ['code' => 4, 'message' => '您还没在借伞平台上注册信息，请先注册借伞后扫描还伞二维码'];
//
//                $text = new Text();
//                $text->content = $result['message'];

//                return $text;
            }
            $umbrellaInfo = $user->umbrellaInfo;
            if ($umbrellaInfo->status == 2) {
                if (!$history = $user->umbrellaHistories()->where('status', 0)->first()) {
                    $result = ['code' => 1, 'message' => '您还未借伞'];
                } else {
                    DB::transaction(function () use ($umbrellaInfo, $history, $station, $user, $now) {
                        $umbrellaInfo->update([
                            'still_at'    => $now,
                            'status'      => 1,
                        ]);
                        $history->update([
                            'still_at'      => $now,
                            'status'        => 1,
                            'still_station' => $station->name
                        ]);
                        $station->increment('amount');
                        if (!$history->umbrella_id) {
                            $user->umbrella()->update(['still_at' => $now, 'station_id' => $station->id]);
                        } else {

                            $user->umbrella()->update(['user_id' => 0, 'still_at' => $now, 'station_id' => $station->id]);
                        }
                    }, 3);

                    $result = ['code' => 0, 'message' => '还伞成功', 'user' => $user, 'history' => $history];

                    event(new TriggerUmbrellaNotice($openid, $result, 'stillNew'));

                    return '';
                    exit;
                }
            } else {
                $result = ['code' => 3, 'message' => '您已经完成注册，请点击下方菜单栏进行借伞'];
            }
        } catch (\Exception $exception) {
            Log::alert('站点扫码异常：' . $station->name);
            Log::alert($exception);
            $result = ['code' => 4, 'message' => '系统繁忙，请稍后重试'];
        }


        $text = new Text();
        $text->content = $result['message'];

        return $text;
    }

    private function forceStill($openid)
    {
        try {
            $user = User::where('openid', $openid)->first();
            $now = Carbon::now()->toDateTimeString();
            Log::alert('强制扫码id:' . $user->id);
            if ($user->umbrellaInfo->status == 1) {
                $result = ['code' => 2, 'message' => '请扫码雨伞上的二维码进行借伞'];
            } else {
                if (is_null($history = $user->umbrellaHistories()->where('status', 0)->first())) {
                    DB::transaction(function () use ($user, $now) {
                        $user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
                        $user->umbrellaInfo()->increment('force_count');
                    });
                } else {
                    if (is_null($user->umbrella)) {
                        DB::transaction(function () use ($history, $user, $now) {
                            $history->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->increment('force_count');
                        }, 3);
                    } else {
                        DB::transaction(function () use ($history, $user, $now) {
                            $history->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->update(['status' => 1, 'still_at' => $now]);
                            $user->umbrellaInfo()->increment('force_count');
                            $user->umbrella()->update(['user_id' => 0, 'still_at' => $now]);
                        }, 3);
                    }
                }

                $result = ['code' => 0, 'message' => '还伞成功！'];
            }
        } catch (\Exception $exception) {
            $result = ['code' => 2, 'message' => '系统繁忙，请稍后重试'];
            $error_sign = 'umbrella_force_still_error';
            event(new TriggerWarning('万能备用码出现异常，请搜索'. $error_sign .'查看异常日志'));
            Log::warning($error_sign.':'.$exception);
        }

        $text = new Text();
        $text->content = $result['message'];

        return $text;
    }

    public function signCard($message)
    {
        $text = new Text();

        //get timestamp
        $currentDatetime = Carbon::now()->toDateTimeString();
        $startDatetime = Carbon::createFromTime(5, 0, 0)->toDateTimeString();
        $endDatetime = Carbon::createFromTime(10, 0, 0)->toDateTimeString();
        $currentTimestamp = strtotime($currentDatetime);
        $startTimestamp = strtotime($startDatetime);
        $endTimestamp = strtotime($endDatetime);
        $openid = $message->FromUserName;
        $result = GetUp::where('open_id', $openid)->first();
        if (!User::firstCreateOrUpdate($openid)) {
            event(new TriggerWarning('签到打卡用户初始化失败，请查看日志'));
            $text->content = '初始化用户失败，请稍后重新点击';

            return $text;
        }


        if (!empty($result)) {
            if ($currentTimestamp > $endTimestamp) {
                if (Carbon::parse($result->last_get_up_datetime)->isToday()) {
                    $text->content = '您已签到打卡，打卡时间为：'.$result->last_get_up_datetime.'.<a href="'. url('wechat/getup/index') .'">请点击查看排行榜</a>';;
                } else {
                    $text->content = '你迟到了哦，签到时间是每天早上5点到10点，明天别再错过了哦！<a href="'. url('wechat/getup/index') .'">请点击查看排行榜</a>';
                }
            } else if ($currentTimestamp < $startTimestamp) {
                $text->content = '还没开始哦，先别着急！';
            } else {
                $lastGetUpDatetime = $result->last_get_up_datetime;
                $lastGetUpTimestamp = strtotime($lastGetUpDatetime);
                if ($lastGetUpTimestamp > $startTimestamp) {
                    $text->content = '您已签到打卡，打卡时间为：'.$lastGetUpDatetime.'.<a href="'. url('wechat/getup/index') .'">请点击查看排行榜</a>';
                } else {
                    $dayDuration = $result->day_duration;
                    $daySum = $result->day_sum;
                    $dayTotal = $result->day_total;
                    DB::table('get_up')
                        ->where('open_id', $openid)
                        ->update([
                            'last_get_up_datetime' => $currentDatetime,
                            'day_duration' => ++$dayDuration,
                            'day_sum' => ++$daySum,
                            'day_total' => ++$dayTotal
                        ]);
                    DB::table('get_up_history')
                        ->insert([
                            'open_id' => $openid,
                            'get_up_datetime' => $currentDatetime
                        ]);
                    $countGetUp = DB::table('get_up')
                        ->where('last_get_up_datetime', '>=', $startDatetime)
                        ->count();
                    $result = GetUp::where('open_id', $openid)->first();
//                    $text->content = '签到成功！您是今天第' . $countGetUp . '个早起的人，已连续坚持' . $dayDuration . '天，累计' . $daySum . '天。<a href="'. url('wechat/getup/index') .'">请点击查看排行榜</a>';
                    event(new TriggerGetUpNotice($openid, $result, 'everyday'));
                    exit;
                }
            }
        } else {

            if ($currentTimestamp > $endTimestamp) {

                DB::table('get_up')->insert([
                    'open_id' => $openid
                ]);

                $text->content = '你迟到了哦，签到时间是每天早上5点到10点，明天别再错过了哦！<a href="'. url('wechat/getup/index') .'">请点击查看排行榜</a>';

            } else if ($currentTimestamp < $startTimestamp) {
                DB::table('get_up')->insert([
                    'open_id' => $openid
                ]);
                $text->content = '还没开始哦，先别着急！';
            } else {
                DB::table('get_up')
                    ->insert([
                        'open_id' => $openid,
                        'last_get_up_datetime' => $currentDatetime,
                        'day_duration' => 1,
                        'day_sum' => 1,
                        'day_total' => 1
                    ]);
                DB::table('get_up_history')
                    ->insert([
                        'open_id' => $openid,
                        'get_up_datetime' => $currentDatetime
                    ]);
                $countGetUp = DB::table('get_up')
                    ->where('last_get_up_datetime', '>=', $startDatetime)
                    ->count();

//                $text->content = '签到成功！您是今天第' . $countGetUp . '个早起的人，已连续坚持1天，累计1天。 <a href="'. url('wechat/getup/index') .'">请点击查看排行榜</a>';
                $result = GetUp::where('open_id', $openid)->first();
                event(new TriggerGetUpNotice($openid, $result, 'everyday'));
                exit;
            }
        }


        return $text;
    }

    public function school_badge_subscribe()
    {
        $url = 'http://service.lewitech.cn/wechat/badge';
//        $url = route('wechat.badge.index');
        $new = new News([
            'title' => '校徽生成器',
            'url' => $url,
            'description' => '校徽生成器',
            'image' => 'http://wj.qn.h-hy.com/images/lewitech/badge/index-top-bg.png'
        ]);

        return $new;
    }
}
