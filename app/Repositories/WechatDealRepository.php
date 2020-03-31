<?php
namespace App\Repositories;

use App\Events\TriggerWarning;
use App\Models\User;
use App\Models\WechatDeal;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WechatDealRepository
{
    const ORDER_STATUS = 'ORDER';
    const FAIL_STATUS = 'FAIL';
    const SUCCESS_STATUS = 'PAID';
    const BACK_STATUS = 'BACK';
    const BACK_SUCCESS_STATUS = 'BACK_SUCCESS';
    const BACK_ERROR_STATUS = 'BACK_ERROR';
    const TRADE_TYPE = 'JSAPI';
    const NOTIFY_URL = 'http://wx.lewitech.cn/wechat/pay/response';

    private $notify_url;

    private $wechat_deal;

    /**
     * @var User $user
     */
    private $user;

    /**
     * @var Application
     */
    private $wechat;

    private $trade_no;
    private $total_fee;

    public function __construct(WechatDeal $wechatDeal)
    {
        $this->wechat_deal = $wechatDeal;
        $this->wechat = app('wechat');
    }

    private function setUser(User $user = null)
    {
        if (!is_null($user)) {
            $this->user = $user;
        } else {
            $this->user = Auth::user();
        }
    }

    private function setTradeNo($trade = null)
    {
        if (!is_null($trade)) {
            $this->trade_no = $trade;
        } else {
            $this->trade_no = 'wechat_'.$this->str_random(6).'_'.time();
        }
    }

    private function setTotalFee($money = null)
    {
        if (!is_null($money)) {
            $this->total_fee = (int)($money * 100);
        } else {
            $this->total_fee = 1;
        }
    }

    private function setNotifyUrl($notify_url = null)
    {
        if (!is_null($notify_url)) {
            $this->notify_url = $notify_url;
        } else {
            $this->notify_url = self::NOTIFY_URL;
        }
    }


    public function wechatPayOrder($body = '微信默认付款', $money = null, $notify_url = null, $trade = null, User $user = null)
    {
        $this->setUser($user);
        $this->setTradeNo($trade);
        $this->setTotalFee($money);
        $this->setNotifyUrl($notify_url);
        $attr = [
            'trade_type' => self::TRADE_TYPE,
            'body' => $body,
            'out_trade_no' => $this->trade_no,
            'total_fee' => $this->total_fee,
            'openid' => $this->user->openid,
            'notify_url' => $this->notify_url,
        ];
        $config = $this->getPayConfig($attr);
        $this->user->wechatDeals()->create([
            'description' => $body,
            'openid' => $this->user->openid,
            'result_code' => self::ORDER_STATUS,
            'trade_type' => self::TRADE_TYPE,
            'total_fee' => $this->total_fee,
            'out_trade_no' => $this->trade_no,
        ]);

        return $config;
    }

    private function getPayConfig(array $attr)
    {
        $config = null;
        try {
            $order = new Order($attr);
            $payment = $this->wechat->payment;
            $result = $payment->prepare($order);
            $prepay_id = null;
            if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
                $prepay_id = $result->prepay_id;
            }
            if (!is_null($prepay_id)) {
                $config = $payment->configForJSSDKPayment($prepay_id);
                $cache_no = md5($this->trade_no);
                Cache::put($cache_no, 0, 20);
                $config = array_add($config, 'out_trade_no', $cache_no);
            }
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('微信支付-微信交互异常'));
        }

        return $config;
    }

    public function signDonateResponse()
    {
        return $this->baseResponse(function (WechatDeal $order) {
            if ($order->signDonate) {
                $signDonate = $order->user->signDonates()->where('type', 'cash')->orderBy('created_at', 'desc')->first();
                if ($signDonate) {
                    $signDonate->wechat_deal_id = $order->id;
                    $signDonate->save();
                } else {
                    $order->user->signDonates()->create([
                        'type' => 'cash',
                        'wechat_deal_id' => $order->id,
                        'remind' => 1
                    ]);
                }
            } else {
                $order->user->signDonates()->create([
                    'type' => 'cash',
                    'wechat_deal_id' => $order->id,
                    'remind' => 1
                ]);
            }
            $order->user->sign_info->reward = 0;
            $order->user->sign_info->save();
        });
    }

    public function baseResponse(\Closure $closure = null)
    {
        $response = $this->wechat->payment->handleNotify(function ($notify, $successful) use ($closure) {
            $order = $this->wechat_deal->where('out_trade_no', $notify->out_trade_no)->first();
            if ($order) {
                $order->transaction_id = $notify->transaction_id;
                if ($successful) {
                    $order->result_code = self::SUCCESS_STATUS;
                    $order->paid_at     = Carbon::parse($notify->time_end)->toDateTimeString();
                    $order->cash_fee    = $notify->cash_fee;
                    $order->bank_type   = $notify->bank_type;
                    Cache::put(md5($notify->out_trade_no), 1, 20);
                    if (!empty($closure)) {
                        if ($closure instanceof \Closure) {
                            $closure($order);
                        }
                    }
                } else {
                    $order->result_code  = self::FAIL_STATUS;
                    $order->err_code     = $notify->err_code;
                    $order->err_code_des = $notify->err_code_des;
                    Cache::put(md5($notify->out_trade_no), 2, 20);
                }

                $order->save();
            } else {
                return 'Order not exist';
            }

            return true;
        });

        return $response;
    }

    /**
     * 用户支付异步响应
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function response()
    {
        $response = $this->wechat->payment->handleNotify(function ($notify, $successful) {
            $order = $this->wechat_deal->where('out_trade_no', $notify->out_trade_no)->first();
            if ($order) {
                $order->transaction_id = $notify->transaction_id;
                if ($successful) {
                    $order->result_code = self::SUCCESS_STATUS;
                    $order->paid_at     = Carbon::parse($notify->time_end)->toDateTimeString();
                    $order->cash_fee    = $notify->cash_fee;
                    $order->bank_type   = $notify->bank_type;
                    Cache::put(md5($notify->out_trade_no), 1, 20);
                } else {
                    $order->result_code  = self::FAIL_STATUS;
                    $order->err_code     = $notify->err_code;
                    $order->err_code_des = $notify->err_code_des;
                    Cache::put(md5($notify->out_trade_no), 2, 20);
                }

                $order->save();
            } else {
                return 'Order not exist';
            }

            return true;
        });

        return $response;
    }

    /**
     * 退还押金
     *
     * @param WechatDeal $wechatDeal
     * @return WechatDeal|null
     */
    public function refund(WechatDeal $wechatDeal)
    {
        $result = $this->wechat->payment->refund($wechatDeal->out_trade_no, $wechatDeal->out_trade_no.'_back', $wechatDeal->total_fee);
        if ($result->return_code == 'SUCCESS' || $result->result_code == 'SUCCESS') {
            $wechatDeal->refund_at = Carbon::now()->toDateTimeString();
            $wechatDeal->refund_id = $result->refund_id;
            $wechatDeal->out_refund_no = $wechatDeal->out_trade_no.'_back';
            $wechatDeal->result_code = self::BACK_SUCCESS_STATUS;
            $wechatDeal->save();

            return $wechatDeal;
        } else {
            return null;
        }
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
}