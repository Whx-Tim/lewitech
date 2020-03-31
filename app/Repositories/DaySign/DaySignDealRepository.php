<?php
namespace App\Repositories\DaySign;

use App\Events\TriggerWarning;
use App\Library\Traits\SelfClass;
use App\Models\DaySign\DaySign;
use App\Models\DaySign\DaySignDeal;
use App\Models\User;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DaySignDealRepository
{
    use SelfClass;

    const ORDER_STATUS = 'ORDER'; //下单
    const SUCCESS_STATUS = 'SUCCESS'; //成功
    const FAIL_STATUS = 'FAIL'; //交易失败
    const BACK_STATUS = 'BACK'; //退款
    const BACK_FAIL_STATUS = 'BACK_FAIL'; //退款失败
    const BACK_SUCCESS_STATUS = 'BACK_SUCCESS'; // 退款成功
    const CLOSE_STATUS = 'CLOSE'; //失败后押金回收
    const TRADE_TYPE = 'JSAPI'; // 使用的交易类型
    const NOTIFY_URL = 'http://wx.lewitech.cn/wechat/daysign/response'; // 支付结果通知url

    private $model;

    /**
     * @var Application
     */
    private $wechat;

    private $deposit = 1;

    private $tradeNo;

    /**
     * @var User
     */
    private $user;

    private $timer;

    public function __construct()
    {
        $this->model = new DaySignDeal();
        $this->wechat = app('wechat');
        $this->timer = DaySignRepository::self()->getTimer();
    }

    public function isApply(User $user)
    {
        return $this->model->where('user_id', $user->id)
                    ->where('day_sign_id', $this->timer->id)
                    ->where('result_code', self::SUCCESS_STATUS)
                    ->first() ? : false;
    }

    public function order($timer_id, User $user)
    {
        $this->setUser($user);
        $this->setTradeNo();
        $config = $this->getOrderConfig();
        $cache_no = md5($this->tradeNo);
        Cache::put($cache_no, 0, 20);
        $config = array_add($config, 'out_trade_no', $cache_no);
        $this->generateOrder([
            'openid' => $this->user->openid,
            'result_code' => self::ORDER_STATUS,
            'trade_type' => self::TRADE_TYPE,
            'total_fee' => $this->deposit * 100,
            'out_trade_no' => $this->tradeNo,
            'day_sign_id' => $timer_id,
        ]);

        return $config;
    }

    /**
     * 用户支付异步响应
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response(Request $request)
    {
        $response = $this->wechat->payment->handleNotify(function ($notify, $successful) {
            $order = $this->model->where('out_trade_no', $notify->out_trade_no)->first();
            if ($order) {
                $order->transaction_id = $notify->transaction_id;
                if ($successful) {
                    $order->result_code = self::SUCCESS_STATUS;
                    $order->time_end    = Carbon::parse($notify->time_end)->toDateTimeString();
                    $order->cash_fee    = $notify->cash_fee;
                    $order->bank_type   = $notify->bank_type;
                    Cache::put(md5($notify->out_trade_no), 1, 20);
                    $order->save();
//                    $reward = $notify->total_fee / 100;
//                    $this->timer->reward += $reward;
//                    $this->timer->save();
                    $this->applySuccessCache($order->openid);
                } else {
                    $order->result_code  = self::FAIL_STATUS;
                    $order->err_code     = $notify->err_code;
                    $order->err_code_des = $notify->err_code_des;
                    $order->save();
                    Cache::put(md5($notify->out_trade_no), 2, 20);
                }


            } else {
                $openid = $notify->openid;
                $user = User::where('openid', $openid)->first();
                if (!$user) {
                    User::firstCreateOrUpdate($openid);
                    $user = User::where('openid', $openid)->first();
                }
                $this->setUser($user);
                $fillable = $this->model->getFillable();
                $fillable = array_except_values($fillable, ['user_id', 'out_refund_no', 'refund_id', 'refund_at']);
                $this->generateOrder(array_filter_empty(collect($notify)->only($fillable)->toArray()));
            }

            return true;
        });

        return $response;
    }

    public function checkOrder(DaySign $daySign, User $user)
    {
        $deals = $user->daySignDeals()->where('day_sign_id', $daySign->id)->where('result_code', self::ORDER_STATUS)->orderBy('created_at', 'desc')->get();
        foreach ($deals as $deal) {
            $result = $this->wechat->payment->query($deal->out_trade_no);
            if ($result->result_code == 'SUCCESS' && $result->return_code == 'SUCCESS') {
                if ($result->result_code == 'SUCCESS') {
                    $deal->result_code = self::SUCCESS_STATUS;
                    $deal->time_end    = Carbon::parse($result->time_end)->toDateTimeString();
                    $deal->cash_fee    = $result->cash_fee;
                    $deal->bank_type   = $result->bank_type;
                    $deal->save();

                    return 1;
                } else {
                    $deal->result_code  = self::FAIL_STATUS;
                    $deal->err_code     = $result->err_code;
                    $deal->err_code_des = $result->err_code_des;
                    $deal->save();

                    return 2;
                }
            }
        }


        return 0;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }


    private function setTradeNo()
    {
        $this->tradeNo = 'sign_' . $this->str_random(10).time();
    }

    /**
     * 随机字符串
     *
     * @param $length
     * @return string
     */
    private function str_random($length)
    {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $number = mt_rand(97,122);
            $str .= chr($number);
        }

        return $str;
    }

    /**
     * 获取微信统一下单配置信息
     *
     * @return array|string
     */
    private function getOrderConfig()
    {
        $attr = [
            'trade_type' => self::TRADE_TYPE,
            'body' => '早起签到押金缴纳',
            'out_trade_no' => $this->tradeNo,
            'total_fee' => $this->deposit * 100,
            'openid' => $this->user->openid,
            'notify_url' => self::NOTIFY_URL,
        ];
        $config = null;

        try {
            $order = new Order($attr);
            $payment = $this->wechat->payment;
            $result = $payment->prepare($order);
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
                $prepay_id = $result->prepay_id;
            }
            $config = $payment->configForJSSDKPayment($prepay_id);
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('早起打卡3.0押金缴纳-微信交互异常'));
        }

        return $config;
    }

    /**
     * 生成报名订单
     *
     * @param array $data
     */
    private function generateOrder(array $data)
    {
        try {
            $this->user->daySignDeals()->create($data);
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('早起打卡3.0押金缴纳-数据录入异常'));
        }
    }

    public function getTimerApplyUserCount(DaySign $daySign)
    {
        return $this->model->where('day_sign_id', $daySign->id)->where('result_code', self::SUCCESS_STATUS)->count();
    }

    public function getTimerApplyUser(DaySign $daySign, $get = ['*'])
    {
        return $this->model->where('day_sign_id', $daySign->id)->where('result_code', self::SUCCESS_STATUS)->get($get);
    }



}