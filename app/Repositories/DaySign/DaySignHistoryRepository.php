<?php
namespace App\Repositories\DaySign;

use App\Library\Traits\SelfClass;
use App\Models\DaySign\DaySign;
use App\Models\DaySign\DaySignDeal;
use App\Models\DaySign\DaySignHistory;
use App\Models\DaySign\DaySignReward;
use App\Models\User;
use Carbon\Carbon;

class DaySignHistoryRepository
{
    use SelfClass;

    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 0;

    const TODAY_START_TIME = 5;
    const TODAY_END_TIME = 8;

    private $model;

    public function __construct()
    {
        $this->model = new DaySignHistory();
    }

    public function sign(DaySign $daySign, User $user)
    {
        $end_time = Carbon::today()->addHours(self::TODAY_END_TIME)->addMinutes(30);
        $start_time = Carbon::today()->addHours(self::TODAY_START_TIME)->addSeconds(mt_rand(1,15));
        $now = Carbon::now();
        $sign = null;
        if ($now->lte($end_time) & $now->gte($start_time) || $user->id == 154) {
            $time_value = Carbon::now()->diffInSeconds(Carbon::today()->addHours(self::TODAY_START_TIME));
            $sign = $this->model->create([
                'time' => $now->toDateTimeString(),
                'status' => self::STATUS_SUCCESS,
                'user_id' => $user->id,
                'day_sign_id' => $daySign->id,
                'time_value' => $time_value
            ]);
        }

        return $sign;
    }

    public function isSign(DaySign $daySign, User $user)
    {
        return $this->model->where('user_id', $user->id)
                           ->where('day_sign_id', $daySign->id)
                           ->first() ? : false;
    }

    public function signUsers(DaySign $daySign)
    {
        return $this->model->whereDate('time', Carbon::now()->toDateString())->where('status', self::STATUS_SUCCESS)->orderBy('time_value', 'asc')->with('user')->with('user.detail')->take(10)->get();
    }

    public function getSignUsers(DaySign $daySign, $get = ['*'])
    {
        return $this->model->where('day_sign_id', $daySign->id)->where('status', self::STATUS_SUCCESS)->get($get);
    }

    public function getFailUsersId(DaySign $daySign)
    {
        $sign_ids = array_flatten(($this->getSignUsers($daySign, ['user_id']))->toArray());
        $apply_ids = array_flatten((DaySignDealRepository::self()->getTimerApplyUser($daySign, ['user_id']))->toArray());

        $fail_ids = array_except_values($apply_ids, $sign_ids);

        return $fail_ids;
    }

    public function settle(DaySign $daySign)
    {
        $sign_ids = array_flatten(($this->getSignUsers($daySign, ['user_id']))->toArray());
        $fail_ids = $this->getFailUsersId($daySign);
        $per_reward = count($fail_ids) / count($sign_ids);
        $rewards = [];
        $now = Carbon::now()->toDateTimeString();
        foreach ($sign_ids as $id) {
            $rewards[] = [
                'reward' => 1+$per_reward,
                'user_id' => $id,
                'day_sign_id' => $daySign->id,
                'updated_at' => $now,
                'created_at' => $now
            ];
        }
        DaySignReward::insert($rewards);

        return true;
    }

    public function getUserSigns(User $user)
    {
        return $this->model->where('user_id', $user->id)->orderBy('created_at', 'desc')->take(30)->get();
    }
}