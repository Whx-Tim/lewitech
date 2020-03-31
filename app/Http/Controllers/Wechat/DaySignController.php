<?php

namespace App\Http\Controllers\Wechat;

use App\Models\DaySign\DaySignHistory;
use App\Models\DaySign\DaySignReward;
use App\Repositories\DaySign\DaySignDealRepository;
use App\Repositories\DaySign\DaySignHistoryRepository;
use App\Repositories\DaySign\DaySignRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DaySignController extends Controller
{
    public function index()
    {
        $timer = DaySignRepository::self()->getTimer();
        $reward = DaySignDealRepository::self()->getTimerApplyUserCount($timer);
        $is_apply = DaySignDealRepository::self()->isApply(Auth::user());
        $is_sign = DaySignHistoryRepository::self()->isSign(DaySignRepository::self()->getTimer(), Auth::user());
        $sign_users = DaySignHistoryRepository::self()->signUsers($timer);

        return view('wechat.daySign.index', compact('is_apply', 'is_sign', 'sign_users', 'reward'));
    }

    public function setting()
    {
        $user = Auth::user();
        $signs = DaySignHistoryRepository::self()->getUserSigns($user);
        $reward = DaySignReward::where('user_id', $user->id)->sum('reward');
        $value = DaySignHistory::where('user_id', $user->id)->avg('time_value');
        $value_total = 12600;
        $value = ($value_total - $value) / $value_total * 100;
        $value = intval($value);

        return view('wechat.daySign.user', compact('user', 'signs', 'reward', 'value'));
    }

    public function order()
    {
        if (DaySignDealRepository::self()->isApply(Auth::user())) {
            return $this->ajaxReturn(2, '您已经报名参与了明日打卡');
        } else {
            $timer = DaySignRepository::self()->getTimer();
            $config = DaySignDealRepository::self()->order($timer->id, Auth::user());

            return $this->ajaxReturn(1, '等待验证支付结果', compact('config'));
        }
    }

    public function sign()
    {
        $timer = DaySignRepository::self()->getTimer();
        if (DaySignHistoryRepository::self()->isSign($timer, Auth::user()) === false) {
            if (is_null(DaySignHistoryRepository::self()->sign($timer, Auth::user()))) {
                return $this->ajaxReturn(2, '打卡还未开始');
            }

            return $this->ajaxReturn(0, '打卡成功');
        } else {
            return $this->ajaxReturn(1, '您今日已经打卡了');
        }
    }

    public function checkOrderPay(Request $request)
    {
        if (Cache::has($request->get('out_trade_no'))) {
            $cache = Cache::get($request->get('out_trade_no'));
            if ($cache == 1) {
                return $this->ajaxReturn(0, '支付成功');
            } else if ($cache == 2){
                return $this->ajaxReturn(2, '支付失败，请重新报名');
            } else {
                return $this->ajaxReturn(1, '等待验证');
            }
        } else {
            $timer = DaySignRepository::self()->getTimer();
            $check_status = DaySignDealRepository::self()->checkOrder($timer, Auth::user());
            if ($check_status == 1) {
                return $this->ajaxReturn(0, '支付成功');
            } else if ($check_status == 2){
                return $this->ajaxReturn(2, '支付失败，请重新报名');
            } else {
                return $this->ajaxReturn(1, '等待验证');
            }
        }
    }

    public function response(Request $request)
    {
        return DaySignDealRepository::self()->response($request);
    }
}
