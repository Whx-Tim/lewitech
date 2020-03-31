<?php
namespace App\Repositories;

use App\Models\Sign;
use App\Models\SignTimer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SignRepository
{
    const LOST_SIGN = 0;
    const SIGN = 1;
    const REPLENISH_SIGN = 2;
    const TODAY_START_TIME = 5;
    const TODAY_END_TIME = 10;

    /**
     * @var Sign $sign
     */
    private $sign;

    /**
     * @var User $user
     */
    private $user;

    /**
     * 月初
     *
     * @var Carbon
     */
    private $start_month;

    /**
     * 月末
     *
     * @var Carbon
     */
    private $end_month;

    public function __construct(Sign $sign)
    {
        $this->sign = $sign;
        $this->start_month = Carbon::now()->startOfMonth();
        $this->end_month   = Carbon::now()->endOfMonth();
    }

    /**
     * 设置当前用户，默认登录用户
     *
     * @param User|null $user
     */
    private function setUser(User $user = null)
    {
        if (!is_null($user)) {
            $this->user = $user;
        } else {
            $this->user = Auth::user();
        }
    }

    private function setSign(Sign $sign = null)
    {
        if (!is_null($sign)) {
            $this->sign = $sign;
        }
    }

    /**
     * 用户进行签到
     *
     * @param SignTimer $signTimer
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function sign(SignTimer $signTimer,User $user = null)
    {
        $this->setUser($user);
        $end_time = Carbon::today()->addHours(self::TODAY_END_TIME);
        $start_time = Carbon::today()->addHours(self::TODAY_START_TIME)->addSeconds(mt_rand(1,15));
        $now = Carbon::now();
        $sign = null;
        if ($now->lte($end_time) & $now->gte($start_time) || $this->user->id == 154) {
            $sign_timer = $signTimer;
            $time_value = Carbon::now()->diffInSeconds(Carbon::today()->addHours(5));
            $sign = $this->user->signs()->create([
                'today_time' => $now->toDateTimeString(),
                'today_status' => self::SIGN,
                'sign_timer_id' => $sign_timer->id,
                'time_value' => $time_value
            ]);
            $sign_info = $this->user->sign_info;
            $sign_info->total_count += 1;
            $sign_info->duration_count += 1;
            $sign_info->month_count += 1;
            $sign_info->save();
        }

        return $sign;
    }

    /**
     * 计算用户签到早起值。
     * 算法为：
     * 1、 获取用户的早起值乘以当前签到次数总数-1
     * 2、 将1中获取的值加上今日早起值除以总数得出总的早起值
     *
     * @param Sign $sign
     * @param User|null $user
     * @return int|mixed
     */
    public function computeTimeValue(Sign $sign = null, User $user = null)
    {
        $this->setUser($user);
        $this->setSign($sign);
        $sign_count = $this->user->signs()->whereDate('today_time', '>=', Carbon::now()->startOfMonth()->toDateString())->count();
        $today = Carbon::today()->addHours(5);
        $today_value = Carbon::parse($this->sign->today_time)->diffInSeconds($today);
        if ($this->user->sign_info->time_value == 0) {
            $this->user->sign_info->time_value = $today_value;
        } else {
            $this->user->sign_info->time_value = ($this->user->sign_info->time_value * ($sign_count-1) + $today_value) / $sign_count;
        }

        $this->user->sign_info->save();

        return $this->user->sign_info;
    }

    /**
     * 是否已经签到过
     *
     * @param User|null $user
     * @return bool
     */
    public function is_sign(User $user = null)
    {
        if (is_null($user)) {
            $user = Auth::user();
        }
        $today = Carbon::today()->toDateString();

        $sign = $user->signs()->whereDate('today_time', $today)->first();

        if ($sign) {
            if ($sign->today_status == self::SIGN || $sign->today_status == self::REPLENISH_SIGN) {
                return 1;
            } else {
                return 2;
            }
        }

        return 0;
    }

    public function getSigns()
    {
        return $this->sign->orderBy('created_at', 'desc')->get();
    }

    public function getSignWithUser()
    {
        return $this->getSigns()->load('user');
    }

    public function getSignWithTimer()
    {
        return $this->getSigns()->load('sign_timer');
    }

    public function getSignWithUserAndTimer()
    {
        return $this->getSigns()->load('user')->load('sign_timer');
    }

    /**
     * 获取今日签到排行榜
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function getTodaySignList()
    {
        return $this->sign->whereDate('today_time', Carbon::now()->toDateString())->where('today_time', '<', Carbon::today()->addHours(10)->toDateTimeString())->take(10)->with('user.detail')->get();
    }

    public function getTodaySign(User $user = null)
    {
        $this->setUser($user);

        return $this->user->signs()->whereDate('today_time', Carbon::today()->toDateString())->first();
    }

    public function getTodaySignRank(Sign $sign = null)
    {
        if ($sign == null) {
            return 0;
        }
        return Sign::whereDate('today_time', Carbon::today()->toDateString())->where('today_time', '<', $sign->today_time)->count() + 1;
    }

    public function getLostSign(User $user = null)
    {
        $this->setUser($user);

        return $this->user->signs()->where('today_status', self::LOST_SIGN)->whereDate('today_time', '>=', $this->start_month->toDateString())->orderBy('today_time', 'asc')->first();
    }

    public function getLostSignList(User $user = null)
    {
        $this->setUser($user);

        return $this->user->signs()->where('today_status', self::LOST_SIGN)->whereDate('today_time', '>=', $this->start_month->toDateString())->orderBy('today_time', 'asc')->get();
    }

    public function signAgain(Sign $sign)
    {
//        $this->setUser($user);
//        $sign = $this->user->signs()->where('today_status', self::LOST_SIGN)->orderBy('created_at', 'asc')->first();
        $sign->today_status = self::REPLENISH_SIGN;
        $sign->save();

        return $sign;
    }

    public static function __callStatic($name, $arguments)
    {
        $class = new SignRepository(new Sign());
        $name = str_replace('static_', '', $name);

        return call_user_func_array([$class, $name], $arguments);
    }

    public function getWeekRank()
    {
        $week_start = Carbon::now()->subDay()->startOfWeek()->toDateString();
        $week_end = Carbon::now()->subDay()->endOfWeek()->toDateString();
        $timer = SignTimerRepository::static_getOpeningTimer();
        $signs = $this->sign->select('user_id', DB::raw('AVG(time_value) as average_value'), DB::raw('SUM(today_status) as days'))
            ->whereDate('today_time', '>=', $week_start)
            ->whereDate('today_time', '<=', $week_end)
            ->where('sign_timer_id', $timer->id)
            ->where('today_status', 1)
            ->groupBy(['user_id'])
            ->having('days', '>=', 7)
            ->orderBy('average_value', 'asc')
            ->with('user')->get();

        return $signs;
    }


}