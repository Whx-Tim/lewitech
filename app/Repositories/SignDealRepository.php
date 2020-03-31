<?php
namespace App\Repositories;

use App\Events\TriggerWarning;
use App\Models\SignCard;
use App\Models\SignDeal;
use App\Models\SignInfo;
use App\Models\SignTimer;
use App\Models\User;
use Carbon\Carbon;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SignDealRepository
{
    const ORDER_STATUS = 'ORDER'; //下单
    const SUCCESS_STATUS = 'SUCCESS'; //成功
    const FAIL_STATUS = 'FAIL'; //交易失败
    const BACK_STATUS = 'BACK'; //退款
    const BACK_FAIL_STATUS = 'BACK_FAIL'; //退款失败
    const BACK_SUCCESS_STATUS = 'BACK_SUCCESS'; // 退款成功
    const CLOSE_STATUS = 'CLOSE'; //失败后押金回收
    const CONTINUE_STATUS = 'CONTINUE'; //继续签到报名
    const CONTINUE_SUCCESS_STATUS = 'CONTINUE_SUCCESS';
    const OVER_STATUS = 'OVER'; // 交易溢出报名
    const OVER_CLOSE_STATUS = 'OVER_CLOSE'; // 交易溢出报名时以往记录修正
    const OVER_BACK_STATUS = 'OVER_BACK'; // OVER押金退还状态
    const TRADE_TYPE = 'JSAPI'; // 使用的交易类型
    const NOTIFY_URL = 'http://wx.lewitech.cn/wechat/sign/response'; // 支付结果通知url

    const DEFAULT_MONEY = 30; //默认报名金额

    private $sign_deal;
    /**
     * @var \EasyWeChat\Foundation\Application
     */
    private $wechat;
    /**
     * @var User
     */
    private $user;
    private $trade_no;

    /**
     * @var int 押金 单位：元
     */
    private $deposit;

    private $timer;

    public function __construct(SignDeal $signDeal)
    {
        $this->sign_deal = $signDeal;
        $this->wechat = app('wechat');
        $this->deposit = self::DEFAULT_MONEY;
        $timer = new SignTimerRepository(new SignTimer());
        if ($timer->getApplyingTimer()) {
            $this->timer = $timer->getApplyingTimer();
        } else {
            $this->timer = $timer->getOpeningTimer();
        }
    }

    /**
     * 检查用户是否支付成功
     *
     * @param SignTimer $signTimer
     * @param User|null $user
     * @return int
     */
    public function checkSuccessful(SignTimer $signTimer, User $user = null)
    {
        $this->setUser($user);
        $deals = $this->user->signDeals()->where('sign_timer_id', $signTimer->id)->where('result_code', self::ORDER_STATUS)->orderBy('created_at', 'desc')->get();
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

    /**
     * 检查用户是否押金报名
     *
     * @param SignTimer $signTimer
     * @param User|null $user
     * @return bool
     */
    public function isApplyDeposit(SignTimer $signTimer, User $user = null)
    {
        $this->setUser($user);
        $deal = $this->user->signDeals()->where('sign_timer_id', $signTimer->id)->where('result_code', self::SUCCESS_STATUS)->orderBy('created_at', 'desc')->first();
        if ($deal) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 续费报名
     * 1.获取用户正在打卡的周期所缴纳的费用。
     * 2.判断用户当前签到状态是否为正常，补签1次，补签2次。
     * 3.若是，则判断当前需要缴纳的费用与上一轮周期缴纳的费用的差值进行三种过程的划分
     * ***$cash_fee过去金额 $this->deposit当前应该缴纳的押金
     * ***a.$cash_fee < $this->deposit时，用户交易记录变为CONTINUE，表示为本轮缴费继续使用，并且需要弥补当前押金的差价。
     * ***b.$cash_fee = $this->deposit时，用户交易记录的周期外键变为当前缴纳押金的周期id，同时不需要缴纳押金。交易记录更改为CONTINUE_SUCCESS
     * ***c.$cash_fee > $this->deposit时，用户过去交易记录置为OVER_CLOSE，表示为过去的交易为溢出报名关闭，同时新建一条记录为OVER代表溢出报名，日后进行退款时，使用现金红包进行退款。
     * 4.若否，则不执行任何操作。
     */
    private function continueApply()
    {
        $apply_timer = SignTimerRepository::static_getApplyingTimer();
        $open_timer = SignTimerRepository::static_getOpeningTimer();

        //超过月份继续报名抵扣-----非正常流程
//        $deals = $this->user->signDeals()->where('sign_timer_id', $open_timer->id-1)->whereIn('result_code', [self::CONTINUE_STATUS, self::SUCCESS_STATUS])->orderByDesc('created_at')->get();
        //流程1.
        $deals = $this->user->signDeals()->where('sign_timer_id', '<>', $apply_timer->id)->whereIn('result_code', [self::CONTINUE_STATUS, self::SUCCESS_STATUS, self::CONTINUE_SUCCESS_STATUS, self::OVER_STATUS])->orderByDesc('created_at')->get();
        $deal = 0;
        foreach ($deals as $item) {
            $deal += $item->cash_fee;
        }
        $openid = empty($item->openid) ? $this->user->detail->openid : $item->openid;
        try {
            if ($deal) {
                //流程2.
                if (in_array($this->user->sign_info->status, [SignInfoRepository::NORMAL_SIGN, SignInfoRepository::RECOVER_FIRST_LOST_SIGN, SignInfoRepository::RECOVER_SECOND_LOST_SIGN])) {
                    $cash_fee = ($deal / 100);
                    //流程3.
                    if ($cash_fee < $this->deposit) {
                        //流程3.a
                        $this->setDeposit($this->deposit - $cash_fee);
                        foreach ($deals as $deal) {
                            $deal->result_code = self::CONTINUE_STATUS;
//                            $deal->sign_timer_id = $apply_timer->id;
                            $deal->save();
                        }
                    } else if ($cash_fee == $this->deposit) {
                        //流程3.b
                        $this->setDeposit(0);
                        foreach ($deals as $deal) {
                            $deal->sign_timer_id = $apply_timer->id;
                            $deal->result_code = self::CONTINUE_SUCCESS_STATUS;
                            $deal->save();
                        }
                        $apply_timer->reward += $this->deposit;
                        $apply_timer->save();
                        $this->applySuccessCache($this->user->openid);
                    } else if ($cash_fee > $this->deposit) {
                        //流程3.c
                        try {
                            DB::transaction(function () use ($cash_fee, $apply_timer, $deals, $openid) {
                                $this->user->sign_info->reward += ($cash_fee - $this->deposit);
                                $this->user->signDeals()->create([
                                    'sign_timer_id' => $apply_timer->id,
                                    'sign_card_id' => NULL,
                                    'openid' => $openid,
                                    'result_code' => self::OVER_STATUS,
                                    'trade_type' => self::TRADE_TYPE,
                                    'total_fee' => $this->deposit * 100,
                                    'cash_fee' => $this->deposit * 100
                                ]);
                                foreach ($deals as $deal) {
                                    $deal->result_code = self::OVER_CLOSE_STATUS;
                                    $deal->save();
                                }
                                $this->user->sign_info->save();
                                $apply_timer->apply_count += 1;
                                $apply_timer->reward += $this->deposit;
                                $apply_timer->save();
                            });
                        } catch (\Exception $exception) {
                            event(new TriggerWarning('用户溢出报名异常，用户id：'.$this->user->id.',溢出金额'.($cash_fee-$this->deposit)));
                            Log::warning($exception);
                        }

                        $this->setDeposit(0);
                        $this->applySuccessCache($this->user->openid);
                    }
                }
            }
        } catch (\Exception $exception) {
            event(new TriggerWarning('签到打卡续费异常'));
            Log::warning($exception);
        }

    }


    /**
     * 微信统一下单
     *
     * @param $timer_id 签到周期id
     * @param User|null $user
     * @param null $card_id
     * @return array|string
     */
    public function order($timer_id, SignCard $signCard = null, User $user = null)
    {
        $this->setUser($user);
        if (!is_null($signCard)) {
            $this->setDeposit($this->deposit * $signCard->card->regulation);

            $signCard = $signCard->id;
        } else {
            $signCard = null;
        }
        $this->setTradeNo();
        $this->continueApply();
        $config = null;
        if ($this->deposit) {
            $config = $this->getOrderConfig();
            $cache_no = md5($this->trade_no);
            Cache::put($cache_no, 0, 20);
            $config = array_add($config, 'out_trade_no', $cache_no);
        }

        $this->generateOrder([
            'openid' => $this->user->openid,
            'result_code' => self::ORDER_STATUS,
            'trade_type' => self::TRADE_TYPE,
            'total_fee' => $this->deposit * 100,
            'out_trade_no' => $this->trade_no,
            'sign_timer_id' => $timer_id,
            'sign_card_id' => $signCard
        ]);


        return $config;
    }

    /**
     * 免费报名后想要收费报名的下单
     */
    public function free2depositOrder($timer_id, User $user = null)
    {
        $this->setUser($user);
        $this->setTradeNo();
        $config = $this->getOrderConfig();
        $cache_no = md5($this->trade_no);
        Cache::put($cache_no, 0, 20);
        $config = array_add($config, 'out_trade_no', $cache_no);
        $this->generateOrder([
            'openid' => $this->user->openid,
            'result_code' => self::ORDER_STATUS,
            'trade_type' => self::TRADE_TYPE,
            'total_fee' => $this->deposit * 100,
            'out_trade_no' => $this->trade_no,
            'sign_timer_id' => $timer_id,
            'sign_card_id' => null
        ]);

        return $config;
    }

    /**
     * 退还押金
     *
     * @param SignDeal $signDeal
     * @return SignDeal|null
     */
    public function refund(SignDeal $signDeal, SignTimer $signTimer = null)
    {
        $result = $this->wechat->payment->refund($signDeal->out_trade_no, $signDeal->out_trade_no.'_back', $signDeal->total_fee);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $signDeal->refund_at = Carbon::now()->toDateTimeString();
            $signDeal->refund_id = $result->refund_id;
            $signDeal->out_refund_no = $signDeal->out_trade_no.'_back';
            $signDeal->result_code = self::BACK_SUCCESS_STATUS;
            $signDeal->save();
//            $signTimer->reward -= ($signDeal->total_fee / 100);
//            $signTimer->save();


            return $signDeal;
        } else {
            return null;
        }
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
            $order = $this->sign_deal->where('out_trade_no', $notify->out_trade_no)->first();
            if ($order) {
                $order->transaction_id = $notify->transaction_id;
                if ($successful) {
                    $order->result_code = self::SUCCESS_STATUS;
                    $order->time_end    = Carbon::parse($notify->time_end)->toDateTimeString();
                    $order->cash_fee    = $notify->cash_fee;
                    $order->bank_type   = $notify->bank_type;
                    $reward = $notify->cash_fee / 100;
                    $this->addReward($order);
                    Cache::put(md5($notify->out_trade_no), 1, 20);
                    $order->save();
                    $this->timer->reward += $reward;
                    $this->timer->save();
                    $this->updateContinueTimer($order->user_id);
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
                $fillable = $this->sign_deal->getFillable();
                $fillable = array_except_values($fillable, ['user_id', 'out_refund_no', 'refund_id', 'refund_at']);
                $this->generateOrder(array_filter_empty(collect($notify)->only($fillable)->toArray()));
            }

            return true;
        });

        return $response;
    }

    private function updateContinueTimer($user_id)
    {
        try {
            $openTimer = SignTimerRepository::static_getOpeningTimer();
            $applyTimer = SignTimerRepository::static_getApplyingTimer();
            $this->sign_deal->where('user_id', $user_id)->where('sign_timer_id', '<>', $applyTimer->id)->where('result_code', self::CONTINUE_STATUS)->update(['sign_timer_id' => $applyTimer->id]);
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('继续报名周期更新异常'));
        }
    }

    /**
     * 奖金添加
     *
     * @param $order
     */
    private function addReward($order)
    {
        try {
            $continue = $this->sign_deal->where('result_code', self::CONTINUE_STATUS)->where('user_id', $order->user->id)->get();
            if ($continue) {
                foreach ($continue as $item) {
                    $this->timer->reward += ($item->total_fee / 100);
                }
            }
        } catch (\Exception $exception) {
            Log::warning($exception);
        }
    }

    private function applySuccessCache($openid)
    {
        try {
            if (Cache::has('sign_apply_user_'.$openid)) {
                $key = Cache::get('sign_apply_user_'.$openid);
                Cache::increment('sign_apply_success_'.$key);
            }
        } catch (\Exception $exception) {
            Log::warning($exception);
        }
    }

    /**
     * 设置当前用户
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

    /**
     * 设置微信商户内部订单号
     */
    private function setTradeNo()
    {
        $this->trade_no = 'sign_' . $this->str_random(10).time();
    }

    public function getUserSuccessDeal(User $user = null)
    {
        $this->setUser($user);

        return $this->user->signDeals()->where('result_code', self::SUCCESS_STATUS)->orderBy('created_at', 'desc')->first();
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
     * 生成报名订单
     *
     * @param array $data
     */
    private function generateOrder(array $data)
    {
        try {
            $this->user->signDeals()->create($data);
        } catch (\Exception $exception) {
            Log::warning($exception);
            event(new TriggerWarning('早起签到押金缴纳-数据录入异常'));
        }
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
            'out_trade_no' => $this->trade_no,
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
            event(new TriggerWarning('早起签到押金缴纳-微信交互异常'));
        }

        return $config;
    }

    /**
     * 设置当前押金
     *
     * @param $deposit
     */
    private function setDeposit($deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * 获取本轮奖金的总金额
     *
     * @return mixed
     */
    public function getOpeningTimerTotalFee()
    {
        $timer = SignTimerRepository::static_getOpeningTimer();

        return $this->sign_deal->where('sign_timer_id', $timer->id)->where(function ($query) {
            $query->where('result_code', self::SUCCESS_STATUS)
                ->orWhere('result_code', self::CONTINUE_STATUS)
                ->orWhere('result_code', self::CLOSE_STATUS);
        })->where('total_fee', '>=', 1500)->sum('total_fee');
    }

    /**
     * 获取本轮签到该退还的金额
     *
     * @return mixed
     */
    public function getOpeningTimerRefundFee()
    {
        $timer = SignTimerRepository::static_getOpeningTimer();
        return $this->sign_deal->where('sign_timer_id', $timer->id)->where('result_code', self::SUCCESS_STATUS)->whereHas('user', function ($query) {
            $query->whereHas('sign_info', function ($q) {
                $q->whereIn('status', [SignInfoRepository::RECOVER_FIRST_LOST_SIGN, SignInfoRepository::RECOVER_SECOND_LOST_SIGN]);
            });
        })->where('total_fee', '>=', 0)->sum('total_fee');
    }

    /**
     * 获取本轮签到成功的金额
     *
     * @return int
     */
    public function getOpeningTimerSuccessFee()
    {
        $timer = SignTimerRepository::static_getOpeningTimer();
        $successes = User::whereHas('signDeals', function ($query) use ($timer) {
            $query->where('sign_timer_id', $timer->id)->where(function ($query) {
                $query->where('result_code', self::SUCCESS_STATUS)
                    ->orWhere('result_code', self::CONTINUE_STATUS);
            });
        })->whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN)->where('duration_count', '>=', 30);
        })->whereHas('signApplies', function ($query) use ($timer) {
            $query->where('timer_id', $timer->id)->where('is_free', 0);
        })->with(['signDeals' => function ($query) use ($timer) {
            $query->where('sign_timer_id', $timer->id)->where(function ($query) {
                $query->where('result_code', self::SUCCESS_STATUS)
                    ->orWhere('result_code', self::CONTINUE_STATUS);
            });
        }])->get();
//        dd($successes);
        $success_fee = 0;
        foreach ($successes as $success) {
            foreach ($success->signDeals as $signDeal) {
                $success_fee += $signDeal->total_fee;
            }
        }

        return $success_fee;
    }

    /**
     * 获取本轮签到周期失败的金额
     *
     * @return int
     */
    public function getOpeningTimerFailFee()
    {
        $timer = SignTimerRepository::static_getOpeningTimer();
        $fail_deals = $this->sign_deal->whereHas('user', function ($query) {
            $query->whereHas('sign_info', function ($q) {
                $q->where('status', SignInfoRepository::FAIL_SIGN)->orWhere(function ($query) {
//                    $query->where('status', SignInfoRepository::NORMAL_SIGN)->where('duration_count', '<', 30);
                });
            });
        })->where('sign_timer_id', '<=', $timer->id)->whereIn('result_code', [self::SUCCESS_STATUS, self::CONTINUE_STATUS, self::CLOSE_STATUS, self::CONTINUE_SUCCESS_STATUS])->where('total_fee', '>', 0)->get();
        $fails_fee = 0;
        foreach ($fail_deals as $deal) {
            $fails_fee += $deal->total_fee;
        }

        return $fails_fee;
    }

    public function getOpeningTimerProbablyFailFee()
    {
        $timer = SignTimerRepository::static_getOpeningTimer();
        $fail_deals = $this->sign_deal->whereHas('user', function ($query) {
            $query->whereHas('sign_info', function ($q) {
                $q->where('status', SignInfoRepository::FAIL_SIGN);
            });
        })->where('sign_timer_id', $timer->id)->where('result_code', self::SUCCESS_STATUS)->where('total_fee', '>', 0)->get();
        $fails_fee = 0;
        foreach ($fail_deals as $deal) {
            $fails_fee += $deal->total_fee;
        }

        return $fails_fee;
    }


    public function getOpeningCanCarveReward()
    {
        return $this->getOpeningTimerFailFee();
    }

    /**
     * 需要退款的用户
     *
     * @return array
     */
    public function refundUsers()
    {
        $next_users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', (SignTimerRepository::static_getApplyingTimer())->id);
        })->get();
        $prev_users = User::whereHas('signApplies', function ($query) {
            $query->where('timer_id', (SignTimerRepository::static_getOpeningTimer())->id);
        })->whereHas('sign_info', function ($query) {
            $query->where('status', SignInfoRepository::NORMAL_SIGN)
                ->where('is_free', 0);
        })->with('signDeals')->get();
        $users = [];
        $deals = [];
        foreach ($prev_users as $prev_user) {
            $status = 0;
            foreach ($next_users as $next_user) {
                if ($prev_user->id == $next_user->id) {
                    $status = 1;
                    break;
                }
            }
            if (!$status) {
                $users []= $prev_user;
                $deals []= $prev_user->signDeals[0];
            }
        }
//        dd([$next_users, $prev_users, $users, $deals]);

        return $deals;
    }

    /**
     * 付费玩家失败扣款，状态变更为close
     *
     * @return bool
     */
    public function failUserClose()
    {
//        dd(SignInfo::where('status', SignInfoRepository::FAIL_SIGN)->where('is_free', 0)->get());
        $deals = $this->sign_deal->whereHas('user', function ($query) {
            $query->whereHas('sign_info', function ($q) {
                $q->whereIn('status', [SignInfoRepository::FAIL_SIGN, SignInfoRepository::FIRST_LOST_SIGN, SignInfoRepository::SECOND_LOST_SIGN]);
            });
        })->where('sign_timer_id', (SignTimerRepository::static_getOpeningTimer())->id)->where(function ($query) {
            $query->whereIn('result_code', [self::SUCCESS_STATUS, self::CONTINUE_SUCCESS_STATUS, self::CONTINUE_STATUS, self::OVER_STATUS]);
        })->with('user.sign_info')->get();

//        dd($deals);
        $id_arr = [];
        foreach ($deals as $deal) {
            $id_arr[] = $deal->id;
        }
        $this->sign_deal->whereIn('id', $id_arr)->update(['result_code' => self::CLOSE_STATUS]);

        return true;
    }

}