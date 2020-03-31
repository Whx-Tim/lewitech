<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatDeal extends Model
{
    protected $fillable = ['description', 'user_id', 'openid', 'result_code', 'err_code', 'err_code_des', 'trade_type', 'bank_type', 'total_fee', 'cash_fee', 'transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id', 'refund_at', 'paid_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function signDonate()
    {
        return $this->hasOne(SignDonate::class, 'wechat_deal_id', 'id');
    }

    //乐微互助关系模型绑定
    public function help()
    {
        return $this->hasOne(HelpUser::class, 'deal_id', 'id');
    }
}
