<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SignDeal extends Model
{
    protected $fillable = ['sign_card_id', 'sign_timer_id', 'user_id', 'openid', 'result_code', 'err_code', 'err_code_des', 'trade_type', 'bank_type', 'total_fee', 'cash_fee', 'transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id', 'refund_at', 'time_end'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function card()
    {
        return $this->belongsTo(SignCard::class, 'sign_card_id', 'id');
    }

    public function timer()
    {
        return $this->belongsTo(SignTimer::class, 'sign_timer_id', 'id');
    }

}
