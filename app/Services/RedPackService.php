<?php
namespace App\Services;

use App\Library\Traits\SelfClass;
use App\Models\RedPack;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RedPackService
{
    use SelfClass;

    private $red_pack_service;
    private $user;

    public function __construct()
    {
        $this->red_pack_service = new RedPack();
    }

    private function set_user(User $user = null)
    {
        if (is_null($user)) {
            if (empty(Auth::user())) {
                throw new \Exception('缺少用户登录态，请传用户参数');
            } else {
                $this->user = Auth::user();
            }
        }  else {
            $this->user = $user;
        }
    }

    /**
     * 签到打卡早起奖金红包发送
     *
     * @param $money
     * @param User|null $user
     * @return mixed
     * @throws \Exception
     */
    public function sign_send($money, User $user = null)
    {
        $this->set_user($user);
        $wechat = app('wechat');
        $luckyMoney = $wechat->lucky_money;
        $bill_no = 'sign'.Carbon::now()->format('Ymd').$this->str_random(10);
        $luckyMoneyData = [
            'mch_billno' => $bill_no,
            'send_name' => '早起打卡',
            're_openid' => $this->user->openid,
            'total_amount' => $money * 100,
            'wishing' => '乐微科技早起打卡奖金红包',
            'act_name' => '乐微科技早起打卡活动',
            'remark' => '乐微科技早起打卡奖金发放'
        ];

        $result = $luckyMoney->sendNormal($luckyMoneyData);
        $data = $this->handleWechatResult($result);
        $data['user_id'] = $this->user->id;

        return $this->red_pack_service->create($data);
    }

    public function day_sign_send($money, User $user)
    {
        $wechat = app('wechat');
        $money = floor($money*100) / 100;
//        dd($money);
        $luckyMoney = $wechat->lucky_money;
        $bill_no = 'daySign'.Carbon::now()->format('Ymd').$this->str_random(5);
        $luckyMoneyData = [
            'mch_billno' => $bill_no,
            'send_name' => '早起打卡',
            're_openid' => $user->openid,
            'total_amount' => $money * 100,
            'wishing' => '早起打卡奖金红包',
            'act_name' => '早起打卡活动',
            'remark' => '早起打卡奖金发放'
        ];

        $result = $luckyMoney->sendNormal($luckyMoneyData);
        $data = $this->handleWechatResult($result);
        $data['user_id'] = $user->id;

        return $this->red_pack_service->create($data);
    }

    /**
     * 签到打卡早起OVER状态的押金退还
     *
     * @param $money
     * @param User|null $user
     * @return mixed
     * @throws \Exception
     */
    public function sign_refund_send($money, User $user = null)
    {
        $this->set_user($user);
        $wechat = app('wechat');
        $luckyMoney = $wechat->lucky_money;
        $bill_no = 'sign'.Carbon::now()->format('Ymd').$this->str_random(10);
        $luckyMoneyData = [
            'mch_billno' => $bill_no,
            'send_name' => '早起打卡',
            're_openid' => $this->user->openid,
            'total_amount' => $money * 100,
            'wishing' => '乐微科技早起打卡押金归还',
            'act_name' => '早起打卡押金归还',
            'remark' => '乐微科技早起打卡押金归还'
        ];

        $result = $luckyMoney->sendNormal($luckyMoneyData);
        $data = $this->handleWechatResult($result);
        $data['user_id'] = $this->user->id;

        return $this->red_pack_service->create($data);
    }

    public function handleWechatResult($result)
    {
        $data['return_code'] = $result->return_code;
        $data['mch_bill_no'] = '-';
        $data['openid'] = '-';
        $data['total_amount'] = 0;
        $data['send_list_id'] = '-';
        if ($result->return_code == 'SUCCESS') {
            $data['result_code'] = $result->result_code;
            if ($result->result_code == 'SUCCESS') {
                $data['mch_bill_no'] = $result->mch_billno;
                $data['openid'] = $result->re_openid;
                $data['total_amount'] = $result->total_amount;
                $data['send_list_id'] = $result->send_listid;
            } else {
                $data['err_code'] = $result->err_code;
                $data['err_code_des'] = $result->err_code_des;
                $data['return_msg'] = $result->return_msg;
            }
        } else {
            $data['return_msg'] = $result->return_msg;
        }

        return $data;
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