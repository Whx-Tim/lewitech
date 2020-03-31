<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatMessage extends Model
{
    public static function messageHandle($message)
    {
//        return 'successful';
        return call_user_func([new self(), $message->MsgType], $message);
    }

    public function text($message)
    {
        return WechatText::handle($message);
//        return '<a href="'. url('wechat/active/list') .'">活动发起</a>';
    }

    public function event($message)
    {
        return WechatEvent::eventHandle($message);
    }

}
