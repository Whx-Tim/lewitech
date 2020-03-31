<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserUmbrella extends Model
{
    protected $guarded = ['_token', '_method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status2string()
    {
        switch ($this->status) {
            case 0:
                return '不可借';
            case 1:
                return '可借雨伞';
            case 2:
                return '正在使用当中';
            case 3:
                return '正在流转当中';
            default:
                return '未知';
        }
    }

    /**
     * 持续时间
     */
    public function standing()
    {
        $now = Carbon::now();
        $time = Carbon::parse($this->borrow_at);
        Carbon::setLocale('zh');
        $diff_time = $time->diffInDays($now);
        if (!$diff_time) {
            $diff_time = $time->diffInHours($now);
            if (!$diff_time) {
                $diff_time = $time->diffInMinutes($now);

                return $diff_time.'分钟';
            }

            return $diff_time.'小时';
        }

        return $diff_time.'天';
//        return mb_substr($diff_time, 0, mb_strlen($diff_time)-1);
    }

    /**
     * 应该归还日期
     *
     * @return string
     */
    public function shouldStillDate()
    {
        $time = Carbon::parse($this->borrow_at);
        $time = $time->addDays(15);
        $time = $time->toDateString();

        return $time.'前';
    }

    public function lastStillDate()
    {
        $time = Carbon::parse($this->borrow_at);
        $time = $time->addDays(16);
        $time = $time->toDateString();

        return $time.'18:00前';
    }
}
