<?php

namespace App\Http\Controllers\Wechat;

use App\Models\WechatDeal;
use App\Repositories\WechatDealRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class WechatDealController extends Controller
{
    private $wechat_deal_repository;

    public function __construct(WechatDealRepository $wechatDealRepository)
    {
        $this->wechat_deal_repository = $wechatDealRepository;
    }

    public function everyDay()
    {
        $user = Auth::user();
        $deals = $user->wechatDeals;

        return view('temp.wechat_pay', compact('deals'));
    }

    public function order()
    {
        $config = $this->wechat_deal_repository->wechatPayOrder('每日流水支付', 150);

        return $this->ajaxReturn(1, '请等待支付验证', compact('config'));
    }

    public function orderResponse(Request $request)
    {
        if (Cache::has($request->get('out_trade_no'))) {
            $cache = Cache::get($request->get('out_trade_no'));
            if ($cache == 1) {
                return $this->ajaxReturn(0, '支付成功');
            } else {
                return $this->ajaxReturn(2, '支付异常');
            }
        } else {
            return $this->ajaxReturn(1, '正在验证支付结果');
        }
    }

    public function refund(WechatDeal $wechatDeal)
    {
        $this->wechat_deal_repository->refund($wechatDeal);

        return $this->ajaxReturn(0, '退款成功');
    }

    public function showSafe()
    {
        if (!session('safe_pay_title')) {
            return redirect()->back()->with(['error_message' => '缺少支付参数']);
        }
        if (!session('safe_pay_money')) {
            return redirect()->back()->with(['error_message' => '缺少支付金额']);
        }

        $title = session('safe_pay_title');
        $money = session('safe_pay_money');

        return view('wechat.pay.safe', compact('title', 'money'));
    }
}
