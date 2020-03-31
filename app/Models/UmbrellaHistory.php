<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UmbrellaHistory extends Model
{
    protected $guarded = ['_token', '_method'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function umbrella()
    {
        return $this->belongsTo(Umbrella::class);
    }

    public function status2string()
    {
        switch ($this->status) {
            case 0:
                return '未完成';
            case 1:
                return '已完成';
            case 2:
                return '异常';
            default:
                return '未知';
        }
    }

    public function diffTime()
    {

        if (is_null($this->still_at)) {
            $now = Carbon::now();
        } else {
            $now = Carbon::parse($this->still_at);
        }
        $time = Carbon::parse($this->borrow_at);
        Carbon::setLocale('zh');
        $diff_time = $time->diffForHumans($now);

        return mb_substr($diff_time, 0, mb_strlen($diff_time)-1);
    }

    public function remindStillDate()
    {
        $date = Carbon::parse($this->borrow_at)->addDays(15);
        $day = $date->day;
        $month = $date->month;
        $date = $month.'月'.$day.'日';

        return $date;
    }
}
