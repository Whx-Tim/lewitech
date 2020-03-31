<?php

namespace App\Http\Controllers\Wechat;

use App\Models\HelpUser;
use App\Services\HelpService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HelpController extends Controller
{
    protected $helpService;

    public function __construct(HelpService $helpService)
    {
        $this->helpService = $helpService;
    }

    public function index()
    {
        $helps = HelpUser::where('status', HelpService::STATUS_PAID)->get();
        $total_amount = count($helps) + 50;
        $total_money = $total_amount*10 + 54000;
        $vip = '未加入';
        $help = Auth::user()->help;
        if ($help) {
            if ($help->status == HelpService::STATUS_PAID) {
                if (Auth::user()->detail->is_shareholder) {
                    $vip = '乐微股东';
                } else {
                    $vip = '乐微互助发起人';
                }
            }
        }

        return view('wechat.help.index', compact('total_amount', 'total_money', 'vip'));
    }

    public function showApply()
    {
        $is_shareholder = Auth::user()->detail->is_shareholder;
        $help = Auth::user()->help;

        return view('wechat.help.apply', compact('is_shareholder', 'help'));
    }

    public function apply(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required',
            'id_number' => 'required',
        ], [
            'name.required' => '请输入您的真实姓名',
            'id_number.required' => '请输入您的身份证号码'
        ]);

        $user = Auth::user();
        $this->helpService->apply($request->only(['name', 'id_number']), $user);
        $config = $this->helpService->getHelpConfig();

        return $this->ajaxReturn(1, '请等待验证支付结果', compact('config'));
    }

    public function checkOrder(Request $request)
    {
        if (Cache::has($request->get('out_trade_no'))) {
            $cache = Cache::get($request->get('out_trade_no'));
            if ($cache == 1) {
                Cache::forget($request->get('out_trade_no'));
                return $this->ajaxReturn(0, '支付成功');
            } else {
                return $this->ajaxReturn(2, '支付异常');
            }
        } else {
            return $this->ajaxReturn(1, '正在验证支付结果');
        }
    }

    public function payResponse()
    {
        return $this->helpService->response();
    }
}
